<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import HomeLayout from '@/layouts/HomeLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import {
    ChevronDown,
    ChevronUp,
    LoaderCircle,
    Shield,
    User,
} from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const isDemoOpen = ref(false);
</script>

<template>
    <HomeLayout>
        <Head title="Log in" />

        <div
            class="flex min-h-[90vh] items-center justify-center px-4 sm:px-6 lg:px-8"
        >
            <div class="w-full max-w-md space-y-8">
                <!-- Header -->
                <div class="text-center">
                    <h1 class="mb-2 text-3xl font-bold text-gray-900">
                        Welcome Back
                    </h1>
                    <p class="text-gray-600">
                        Sign in to your account to continue
                    </p>
                </div>

                <!-- Demo Credentials (Collapsible) -->
                <div
                    class="overflow-hidden rounded-xl border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 shadow-sm"
                >
                    <!-- Toggle Button -->
                    <button
                        @click="isDemoOpen = !isDemoOpen"
                        type="button"
                        class="flex w-full items-center justify-between px-6 py-4 transition-colors hover:bg-blue-100/50"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="rounded-full bg-blue-100 p-2">
                                <svg
                                    class="h-4 w-4 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">
                                Demo Credentials
                            </span>
                        </div>
                        <ChevronDown
                            v-if="!isDemoOpen"
                            class="h-5 w-5 text-gray-600 transition-transform"
                        />
                        <ChevronUp
                            v-else
                            class="h-5 w-5 text-gray-600 transition-transform"
                        />
                    </button>

                    <!-- Collapsible Content -->
                    <div v-show="isDemoOpen" class="space-y-3 px-6 pb-6">
                        <!-- Client Demo -->
                        <div class="mt-2 rounded-lg bg-white p-3 shadow-sm">
                            <div class="mb-2 flex items-center space-x-2">
                                <User class="h-4 w-4 text-gray-600" />
                                <span
                                    class="text-xs font-semibold tracking-wide text-gray-700 uppercase"
                                >
                                    Client Access
                                </span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <code
                                        class="rounded bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-800"
                                    >
                                        client@example.com
                                    </code>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Password:</span>
                                    <code
                                        class="rounded bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-800"
                                    >
                                        00000000
                                    </code>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Demo -->
                        <div class="rounded-lg bg-white p-3 shadow-sm">
                            <div class="mb-2 flex items-center space-x-2">
                                <Shield class="h-4 w-4 text-orange-600" />
                                <span
                                    class="text-xs font-semibold tracking-wide text-orange-700 uppercase"
                                >
                                    Admin Access
                                </span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <code
                                        class="rounded bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-800"
                                    >
                                        admin@example.com
                                    </code>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Password:</span>
                                    <code
                                        class="rounded bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-800"
                                    >
                                        00000000
                                    </code>
                                </div>
                                <div class="mt-2 border-t border-gray-200 pt-2">
                                    <a
                                        href="/admin-secret-url"
                                        class="text-xs font-medium text-orange-600 hover:text-orange-700 hover:underline"
                                    >
                                        → Go to Admin Panel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Message -->
                <div
                    v-if="status"
                    class="rounded-lg border border-green-200 bg-green-50 p-4 text-center"
                >
                    <p class="text-sm font-medium text-green-800">
                        {{ status }}
                    </p>
                </div>

                <!-- Login Form -->
                <div
                    class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm"
                >
                    <Form
                        v-bind="AuthenticatedSessionController.store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <!-- Email Field -->
                        <div>
                            <Label
                                for="email"
                                class="mb-2 block text-sm font-semibold text-gray-900"
                            >
                                Email Address
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="Enter your email"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                            />
                            <InputError :message="errors.email" class="mt-1" />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <Label
                                    for="password"
                                    class="block text-sm font-semibold text-gray-900"
                                >
                                    Password
                                </Label>
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="request()"
                                    class="text-sm font-medium text-orange-600 hover:text-orange-700"
                                    :tabindex="5"
                                >
                                    Forgot password?
                                </TextLink>
                            </div>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                :tabindex="2"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                            />
                            <InputError
                                :message="errors.password"
                                class="mt-1"
                            />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <Label
                                for="remember"
                                class="flex cursor-pointer items-center space-x-3"
                            >
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    :tabindex="3"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                                />
                                <span class="text-sm text-gray-700"
                                    >Remember me for 30 days</span
                                >
                            </Label>
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-orange-600 px-4 py-3 font-semibold text-white transition-colors duration-200 hover:bg-orange-700"
                            :tabindex="4"
                            :disabled="processing"
                            data-test="login-button"
                        >
                            <LoaderCircle
                                v-if="processing"
                                class="mr-2 h-5 w-5 animate-spin"
                            />
                            {{ processing ? 'Signing in...' : 'Sign In' }}
                        </Button>

                        <!-- Sign Up Link -->
                        <div class="border-t border-gray-200 pt-4 text-center">
                            <p class="text-sm text-gray-600">
                                Don't have an account?
                                <TextLink
                                    :href="register()"
                                    :tabindex="5"
                                    class="ml-1 font-semibold text-orange-600 hover:text-orange-700"
                                >
                                    Create one here
                                </TextLink>
                            </p>
                        </div>
                    </Form>
                </div>

                <!-- Additional Info -->
                <div class="text-center">
                    <p class="text-xs text-gray-500">
                        By signing in, you agree to our Terms of Service and
                        Privacy Policy
                    </p>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
