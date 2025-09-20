<script setup>
import { ref, onMounted, watch, computed } from "vue";
import vueFilePond from "vue-filepond";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import { setOptions } from "filepond";
import { usePage } from "@inertiajs/vue3";

// Import locales
import ar_AR from "filepond/locale/ar-ar";
import fr_FR from "filepond/locale/fr-fr";
import es_ES from "filepond/locale/es-es";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    initialFiles: {
        type: Array,
        default: () => [],
    },
    allowedFileTypes: {
        type: Array,
        default: () => [
            "image/jpeg",
            "image/png",
            "image/gif",
            "image/svg+xml",
            "image/webp",
            "image/avif",
        ],
    },
    allowMultiple: {
        type: Boolean,
        default: false,
    },
    maxFiles: {
        type: Number,
        default: 1,
    },
    maxFileSize: {
        type: Number,
        default: 1024 * 1024 * 5, // 5MB
    },
    collection: {
        type: String,
        default: "default",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    theme: {
        type: String,
        default: "light",
        validator: (value) => ["light", "dark"].includes(value),
    },
    width: {
        type: String,
        default: "100%",
    },
});

const emit = defineEmits([
    "update:modelValue",
    "fileAdded",
    "fileRemoved",
    "error",
]);

// Reactive state
const page = usePage();
const files = ref([]);
const tempFolders = ref([]);
const filePondRef = ref(null);

const wrapperStyle = computed(() => ({
    width: props.width,
}));

// Locale configuration
const LOCALE_MAP = {
    ar: ar_AR,
    fr: fr_FR,
    es: es_ES,
    null: null, // English (default)
};

// Initialize FilePond component
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImagePreview
);

// Initialize locale
function initializeLocale() {
    const configuredLocale = page.props.fileUploadConfig?.locale || null;
    const localeOptions = LOCALE_MAP[configuredLocale];

    if (localeOptions) {
        setOptions(localeOptions);
    }
}

// Initialize chunk file size
const chunkFileSize =
    page.props.fileUploadConfig?.chunkSize || 1024 * 1024 * 10; // 10MB

// Parse upload response (handles both JSON and plain text)
function parseUploadResponse(responseText) {
    // Check if it's an XMLHttpRequest object and extract responseText
    if (
        responseText &&
        typeof responseText === "object" &&
        responseText.responseText !== undefined
    ) {
        responseText = responseText.responseText;
    }

    // Handle empty or null responses
    if (!responseText || responseText === "" || responseText === "null") {
        return null;
    }

    try {
        // If it's already a parsed object
        if (typeof responseText === "object" && responseText !== null) {
            return responseText.folder || responseText;
        }

        const response = JSON.parse(responseText);
        return typeof response === "string"
            ? response
            : response.folder || response;
    } catch {
        const stringResponse =
            typeof responseText === "string"
                ? responseText.trim()
                : String(responseText).trim();

        // Check for invalid responses
        if (
            stringResponse.includes("[object") ||
            stringResponse === "null" ||
            stringResponse === ""
        ) {
            console.error("Invalid response format:", stringResponse);
            return null;
        }

        return stringResponse;
    }
}

// Add temporary folder to state
function addTempFolder(folder, file) {
    tempFolders.value.push(folder);
    emit("fileAdded", { folder, file });
    emit("update:modelValue", [...tempFolders.value]);
}

// Handle file revert (removal of temporary files)
function handleRevert(uniqueId, load, error) {
    if (!uniqueId) {
        error("No unique ID provided");
        return;
    }

    const index = tempFolders.value.indexOf(uniqueId);
    if (index === -1) {
        error("File not found");
        return;
    }

    // Optimistically remove from UI
    tempFolders.value.splice(index, 1);
    emit("update:modelValue", [...tempFolders.value]);

    // Send delete request to server
    fetch(route("filepond.revert", { folder: uniqueId }), {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": page.props.csrf_token,
            Accept: "application/json",
        },
    })
        .then((response) => {
            if (response.ok) {
                emit("fileRemoved", { folder: uniqueId, type: "temp" });
                load();
            } else {
                // Restore folder on server error
                tempFolders.value.splice(index, 0, uniqueId);
                emit("update:modelValue", [...tempFolders.value]);
                error("Failed to delete file from server");
            }
        })
        .catch(() => {
            // Restore folder on network error
            tempFolders.value.splice(index, 0, uniqueId);
            emit("update:modelValue", [...tempFolders.value]);
            error("Failed to delete file");
        });
}

// Handle removal of existing files
function handleFileRemove(error, file) {
    if (error) return;

    // Check if this is a local file (existing file)
    if ((file.origin === 3 || file.origin === 1) && file.source) {
        const existingFile = props.initialFiles.find(
            (f) => f.url === file.source
        );

        if (existingFile?.id) {
            emit("fileRemoved", {
                fileId: existingFile.id,
                type: "existing",
                file: existingFile,
            });
        }
    }
}

// Public method to reset component state
function resetFiles() {
    files.value = [];
    tempFolders.value = [];
    emit("update:modelValue", []);

    // Clear FilePond instance if available
    if (filePondRef.value) {
        filePondRef.value.removeFiles();
    }
}

// Server configuration for FilePond
const serverOptions = {
    process: {
        url: route("filepond.upload"),
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": page.props.csrf_token,
        },
        ondata: (formData) => {
            if (props.collection) {
                formData.append("collection", props.collection);
            }
            return formData;
        },
        onload: (response) => {
            // Extract response text from XMLHttpRequest object
            let responseText = response;
            if (
                response &&
                typeof response === "object" &&
                response.responseText !== undefined
            ) {
                responseText = response.responseText;
            }

            const result = parseUploadResponse(responseText);
            if (result) {
                addTempFolder(result, null);
            }
            return result;
        },
        onerror: (response) => {
            console.error("Upload error:", response);
        },
    },
    patch: {
        url: route("filepond.patch") + "?patch=",
        method: "PATCH",
        headers: {
            "X-CSRF-TOKEN": page.props.csrf_token,
        },
        onload: (response) => {
            // Extract response text from XMLHttpRequest object
            let responseText = response;
            if (
                response &&
                typeof response === "object" &&
                response.responseText !== undefined
            ) {
                responseText = response.responseText;
            }

            // For chunk uploads, response might be empty for intermediate chunks
            if (
                !responseText ||
                responseText === "" ||
                responseText === "null"
            ) {
                return null;
            }

            const result = parseUploadResponse(responseText);

            if (result && result !== "null") {
                addTempFolder(result, null);
                return result;
            }
            return null;
        },
        onerror: (response) => {
            console.error("Patch error:", response);
        },
    },
    revert: handleRevert,
    restore: route("filepond.restore") + "?restore=",
    load: (source, load, error) => {
        fetch(source)
            .then((response) => response.blob())
            .then(load)
            .catch(() => error("Could not load file"));
    },
};

// FilePond component options - made reactive with computed
const filePondOptions = computed(() => ({
    server: serverOptions,
    allowMultiple: props.allowMultiple,
    acceptedFileTypes: props.allowedFileTypes,
    maxFiles: props.maxFiles,
    maxFileSize: props.maxFileSize,
    credits: "none",
    disabled: props.disabled,
    required: props.required,
    chunkUploads: true,
    chunkSize: chunkFileSize,
    chunkRetryDelays: [500, 1000, 3000],
    chunkForce: false, // Only chunk files larger than chunkSize
}));

// Watch for external modelValue changes - simplified
watch(
    () => props.modelValue,
    (newValue) => {
        const currentValue = tempFolders.value;
        if (JSON.stringify(newValue) !== JSON.stringify(currentValue)) {
            tempFolders.value = [...(newValue || [])];
        }
    },
    { deep: true }
);

// Initialize component
onMounted(() => {
    initializeLocale();

    // Initialize modelValue
    if (props.modelValue?.length > 0) {
        tempFolders.value = [...props.modelValue];
    }

    // Initialize initial files
    if (props.initialFiles?.length > 0) {
        files.value = props.initialFiles.map((file) => ({
            source: file.url,
            options: { type: "local" },
        }));
    }
});

// Expose public methods
defineExpose({
    resetFiles,
});
</script>

<template>
    <div
        class="filepond-wrapper"
        :class="{ 'filepond-dark': theme === 'dark' }"
        :style="wrapperStyle"
    >
        <FilePond
            ref="filePondRef"
            v-model="files"
            v-bind="filePondOptions"
            :files="files"
            @removefile="handleFileRemove"
        />
        <input
            v-if="required"
            type="hidden"
            :value="tempFolders.length > 0 ? 'has-files' : ''"
            :required="required"
        />
    </div>
</template>
<style scoped>
.filepond-wrapper {
    @apply w-full;
}

/* Light theme */
:deep(.filepond--root) {
    @apply font-sans;
}

:deep(.filepond--panel-root) {
    @apply bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg;
}

:deep(.filepond--drop-label) {
    @apply text-gray-600;
}

:deep(.filepond--item-panel) {
    @apply bg-white border border-gray-200 rounded-lg;
}

:deep(.filepond--file-status-main),
:deep(.filepond--file-info-main) {
    @apply text-gray-800;
}

:deep(.filepond--file-status-sub),
:deep(.filepond--file-info-sub) {
    @apply text-gray-600;
}

/* Force white text for image previews - when image-preview-wrapper exists */
:deep(
        .filepond--file:has(.filepond--image-preview-wrapper)
            .filepond--file-status-main
    ),
:deep(
        .filepond--file:has(.filepond--image-preview-wrapper)
            .filepond--file-info-main
    ) {
    @apply text-white !important;
}

:deep(
        .filepond--file:has(.filepond--image-preview-wrapper)
            .filepond--file-status-sub
    ),
:deep(
        .filepond--file:has(.filepond--image-preview-wrapper)
            .filepond--file-info-sub
    ) {
    @apply text-gray-200 !important;
}

/* Dark theme */
.filepond-dark {
    :deep(.filepond--panel-root) {
        @apply bg-gray-800 border-gray-600;
    }

    :deep(.filepond--drop-label) {
        @apply text-gray-300;
    }

    :deep(.filepond--item-panel) {
        @apply bg-gray-700 border-gray-600;
    }

    :deep(.filepond--file-status-main),
    :deep(.filepond--file-info-main) {
        @apply text-gray-100;
    }

    :deep(.filepond--file-status-sub),
    :deep(.filepond--file-info-sub) {
        @apply text-gray-300;
    }
}
</style>