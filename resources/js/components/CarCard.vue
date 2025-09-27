<script setup lang="ts">
interface Car {
    id: number;
    make: string;
    model: string;
    year: number;
    price_per_day: string;
    description: string;
    fuel_type: string;
    image_url: string;
}

interface Props {
    car: Car;
}

defineProps<Props>();
</script>

<template>
    <div class="group relative overflow-hidden rounded-2xl bg-white shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 border border-gray-100">
        <!-- Car Image with Enhanced Overlay -->
        <div class="relative h-56 overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
            <img
                :src="car.image_url"
                :alt="`${car.make} ${car.model}`"
                class="h-full w-full object-cover transition-all duration-500 group-hover:scale-110"
            />
            
            <!-- Price Badge -->
            <div class="absolute top-4 right-4 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-2 shadow-lg">
                <span class="text-sm font-bold text-white">${{ car.price_per_day }}</span>
                <span class="text-xs text-orange-100">/day</span>
            </div>
            
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
            
            <!-- Quick Action Button -->
            <div class="absolute bottom-4 left-4 opacity-0 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0 translate-y-2">
                <button class="inline-flex items-center rounded-lg bg-white/90 backdrop-blur-sm px-3 py-2 text-sm font-semibold text-gray-900 shadow-lg transition-all hover:bg-white">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View
                </button>
            </div>
        </div>

        <!-- Enhanced Car Details -->
        <div class="p-6 space-y-4">
            <!-- Header -->
            <div class="space-y-2">
                <h3 class="text-xl font-bold text-gray-900 group-hover:text-orange-600 transition-colors">
                    {{ car.make }} {{ car.model }}
                </h3>
                
                <!-- Car Specs -->
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-1">
                        <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-medium">{{ car.year }}</span>
                    </div>
                    
                    <div class="flex items-center gap-1 capitalize">
                        <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="font-medium">{{ car.fuel_type }}</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <p class="line-clamp-2 text-sm text-gray-600 leading-relaxed">
                {{ car.description }}
            </p>

            <!-- Features Icons -->
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>GPS</span>
                </div>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span>Auto</span>
                </div>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2"></path>
                    </svg>
                    <span>A/C</span>
                </div>
            </div>

            <!-- Enhanced Book Button -->
            <button class="group/btn w-full rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3.5 font-semibold text-white shadow-lg transition-all duration-200 hover:from-orange-600 hover:to-orange-700 hover:shadow-xl hover:scale-105 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:outline-none">
                <span class="flex items-center justify-center gap-2">
                    <svg class="h-5 w-5 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Book Now
                </span>
            </button>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>