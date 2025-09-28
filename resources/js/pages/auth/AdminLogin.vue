<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, Lock, Shield } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <div>
        <Head title="Admin Access" />

        <div
            class="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-4 sm:px-6 lg:px-8"
        >
            <div class="w-full max-w-md">
                <!-- Background Pattern -->
                <div
                    class="pointer-events-none absolute inset-0 overflow-hidden"
                >
                    <div
                        class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-orange-500/5 blur-3xl"
                    ></div>
                    <div
                        class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-indigo-500/5 blur-3xl"
                    ></div>
                </div>

                <div class="relative space-y-8">
                    <!-- Header -->
                    <div class="text-center">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-slate-600 bg-slate-700"
                        >
                            <Shield class="h-8 w-8 text-orange-400" />
                        </div>
                        <h1 class="mb-2 text-2xl font-bold text-white">
                            Administrative Access
                        </h1>
                    </div>

                    <!-- Login Form -->
                    <div
                        class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-8 shadow-2xl backdrop-blur-xl"
                    >
                        <Form
                            v-bind="AuthenticatedSessionController.storeAdminLogin.form()"
                            :reset-on-success="['password']"
                            v-slot="{ errors, processing }"
                            class="space-y-6"
                        >
                            <!-- Email Field -->
                            <div>
                                <Label
                                    for="email"
                                    class="mb-2 block text-sm font-semibold text-slate-200"
                                >
                                    Administrator Email
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="email"
                                    placeholder="admin@domain.com"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-3 text-white placeholder-slate-400 transition-colors focus:border-orange-500 focus:bg-slate-700 focus:ring-2 focus:ring-orange-500/20"
                                />
                                <InputError
                                    :message="errors.email"
                                    class="mt-1"
                                />
                            </div>

                            <!-- Password Field -->
                            <div>
                                <div
                                    class="mb-2 flex items-center justify-between"
                                >
                                    <Label
                                        for="password"
                                        class="block text-sm font-semibold text-slate-200"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <Lock class="h-4 w-4" />
                                            <span>Secure Password</span>
                                        </div>
                                    </Label>
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="Enter secure password"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-3 text-white placeholder-slate-400 transition-colors focus:border-orange-500 focus:bg-slate-700 focus:ring-2 focus:ring-orange-500/20"
                                />
                                <InputError
                                    :message="errors.password"
                                    class="mt-1"
                                />
                            </div>

                            <!-- Submit Button -->
                            <Button
                                type="submit"
                                class="flex w-full items-center justify-center rounded-lg bg-gradient-to-r from-orange-600 to-orange-700 px-4 py-3 font-semibold text-white transition-all duration-200 hover:from-orange-700 hover:to-orange-800 hover:shadow-lg hover:shadow-orange-500/25 disabled:cursor-not-allowed disabled:opacity-50"
                                :tabindex="4"
                                :disabled="processing"
                                data-test="admin-login-button"
                            >
                                <LoaderCircle
                                    v-if="processing"
                                    class="mr-2 h-5 w-5 animate-spin"
                                />
                                {{
                                    processing
                                        ? 'Authenticating...'
                                        : 'Access System'
                                }}
                            </Button>

                            <!-- Security Notice -->
                            <div
                                class="border-t border-slate-700 pt-6 text-center"
                            >
                                <div
                                    class="flex items-center justify-center space-x-2 text-slate-400"
                                >
                                    <Shield class="h-4 w-4" />
                                    <p class="text-xs">
                                        Secure connection established
                                    </p>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">
                                    All administrative actions are logged and
                                    monitored
                                </p>
                            </div>
                        </Form>
                    </div>

                    <!-- Footer Warning -->
                    <div class="text-center">
                        <div
                            class="inline-flex items-center space-x-2 rounded-lg border border-amber-800/50 bg-amber-900/20 px-4 py-2 backdrop-blur-sm"
                        >
                            <div
                                class="h-2 w-2 animate-pulse rounded-full bg-amber-400"
                            ></div>
                            <p class="text-xs text-amber-300">
                                Unauthorized access is strictly prohibited
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
