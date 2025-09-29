import { onMounted, ref } from 'vue';

type Appearance = 'light';

export function updateTheme(_: Appearance) {
    if (typeof window === 'undefined') return;

    // Always ensure dark class is removed
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');
}

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') return;

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const appearance = ref<Appearance>('light');

export function initializeTheme() {
    if (typeof window === 'undefined') return;

    // Force light mode
    appearance.value = 'light';
    localStorage.setItem('appearance', 'light');
    setCookie('appearance', 'light');
    updateTheme('light');
}

export function useAppearance() {
    onMounted(() => {
        appearance.value = 'light';
        localStorage.setItem('appearance', 'light');
        setCookie('appearance', 'light');
        updateTheme('light');
    });

    function updateAppearance(_: Appearance) {
        // Ignore any input and always force light
        appearance.value = 'light';
        localStorage.setItem('appearance', 'light');
        setCookie('appearance', 'light');
        updateTheme('light');
    }

    return {
        appearance,
        updateAppearance,
    };
}
