<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {ref, onMounted, onUnmounted} from 'vue';

const props = defineProps({
    qrcode: Array,
})

const currentDate = ref(new Date());
const formattedDate = ref(currentDate.value.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
}));
const formattedTime = ref(currentDate.value.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: 'numeric',
    second: 'numeric',
    hour12: true,
}));

const updateDateTime = () => {
    currentDate.value = new Date();
    formattedDate.value = currentDate.value.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
    formattedTime.value = currentDate.value.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        hour12: true,
    });
};

let intervalId;

onMounted(() => {
    intervalId = setInterval(updateDateTime, 1000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-center">
                    <div class="p-8">
                        <h1
                            class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-5xl dark:text-white">
                            ICTU OJT Attendance Tracker</h1>
                        <p class="mb-4 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 xl:px-48 dark:text-gray-400">
                            {{ formattedDate }}
                        </p>
                        <h1 class="mb-6 text-lg font-bold text-gray-500 lg:text-4xl sm:px-16 xl:px-48 dark:text-gray-400">
                            {{ formattedTime }}
                        </h1>
                        <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 xl:px-48 dark:text-gray-400">
                            Scan the QR Code to store your login or logout</p>
                        <div v-for="qr in qrcode" :key="qr.id" class="flex justify-center">
                            <img :src="'data:image/png;base64,' + qr.code" :alt="qr.type"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
