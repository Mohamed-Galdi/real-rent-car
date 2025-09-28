<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import HomeLayout from '@/layouts/HomeLayout.vue';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <HomeLayout>
        <Head title="Register" />

        <div
            class="flex min-h-screen items-center justify-center bg-white px-4 py-12 sm:px-6 lg:px-8"
        >
            <div class="w-full max-w-md space-y-8">
                <!-- Header -->
                <div class="text-center">
                    <h2 class="mb-2 text-3xl font-bold text-gray-900">
                        Create Account
                    </h2>
                    <p class="text-gray-600">
                        Join us and start your car rental journey
                    </p>
                </div>

                <!-- Form -->
                <div
                    class="rounded-lg border border-gray-200 bg-white p-8 shadow-sm"
                >
                    <Form
                        v-bind="RegisteredUserController.store.form()"
                        :reset-on-success="[
                            'password',
                            'password_confirmation',
                        ]"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <!-- Name Field -->
                        <div>
                            <Label
                                for="name"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Full Name
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="name"
                                name="name"
                                placeholder="Enter your full name"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                            />
                            <InputError
                                :message="errors.name"
                                class="mt-1 text-sm text-red-600"
                            />
                        </div>

                        <!-- Email Field -->
                        <div>
                            <Label
                                for="email"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Email Address
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                required
                                :tabindex="2"
                                autocomplete="email"
                                name="email"
                                placeholder="Enter your email address"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                            />
                            <InputError
                                :message="errors.email"
                                class="mt-1 text-sm text-red-600"
                            />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <Label
                                for="password"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Password
                            </Label>
                            <Input
                                id="password"
                                type="password"
                                required
                                :tabindex="3"
                                autocomplete="new-password"
                                name="password"
                                placeholder="Create a password"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                            />
                            <InputError
                                :message="errors.password"
                                class="mt-1 text-sm text-red-600"
                            />
                        </div>

                        <!-- Confirm Password Field -->
                        <div>
                            <Label
                                for="password_confirmation"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Confirm Password
                            </Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                required
                                :tabindex="4"
                                autocomplete="new-password"
                                name="password_confirmation"
                                placeholder="Confirm your password"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                            />
                            <InputError
                                :message="errors.password_confirmation"
                                class="mt-1 text-sm text-red-600"
                            />
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-orange-500 px-4 py-3 font-semibold text-white transition-colors duration-200 hover:bg-orange-600"
                            tabindex="5"
                            :disabled="processing"
                            data-test="register-user-button"
                        >
                            <LoaderCircle
                                v-if="processing"
                                class="mr-2 h-5 w-5 animate-spin"
                            />
                            Create Account
                        </Button>

                        <!-- Login Link -->
                        <div class="border-t border-gray-200 pt-4 text-center">
                            <p class="text-sm text-gray-600">
                                Already have an account?
                                <TextLink
                                    :href="login()"
                                    class="font-medium text-orange-500 transition-colors hover:text-orange-600"
                                    :tabindex="6"
                                >
                                    Sign in here
                                </TextLink>
                            </p>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
