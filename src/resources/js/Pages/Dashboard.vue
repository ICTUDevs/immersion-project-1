<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    qrcode: Array,
});

const currentDate = ref(new Date());
const formattedDate = ref(
    currentDate.value.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    })
);
const formattedTime = ref(
    currentDate.value.toLocaleTimeString("en-US", {
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
        hour12: true,
    })
);

const updateDateTime = () => {
    currentDate.value = new Date();
    formattedDate.value = currentDate.value.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
    formattedTime.value = currentDate.value.toLocaleTimeString("en-US", {
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
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
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-center"
                >
                    <div class="grid grid-flow-col grid-cols-2 gap-2">
                        <div class="p-8">
                            <h1
                                class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 dark:text-white"
                            >
                                ICTU OJT Attendance Tracker
                            </h1>
                            <p
                                class="mb-4 lg:text-xl font-normal text-gray-500 dark:text-gray-400"
                            >
                                {{ formattedDate }}
                            </p>
                            <h1
                                class="mb-6 lg:text-2xl font-bold text-gray-500 dark:text-gray-400"
                            >
                                {{ formattedTime }}
                            </h1>
                            <p
                                class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400"
                            >
                                Scan the QR Code to store your login or logout
                            </p>
                            <div class="flex justify-center h-52">
                                <img
                                    :src="
                                        'data:image/png;base64,' + qrcode.code
                                    "
                                    :alt="qrcode.type"
                                />
                            </div>
                        </div>
                        <div class="p-8 flex justify-center">
                            <div
                                class="w-full max-w-md p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700"
                            >
                                <div
                                    class="flex items-center justify-between mb-4"
                                >
                                    <h5
                                        class="text-xl font-bold leading-none text-gray-900 dark:text-white"
                                    >
                                        On Duty
                                    </h5>
                                </div>
                                <div class="flow-root">
                                    <ul
                                        role="list"
                                        class="divide-y divide-gray-200 dark:divide-gray-700"
                                    >
                                        <li class="py-3 sm:py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <img
                                                        class="w-8 h-8 rounded-full"
                                                        src="/docs/images/people/profile-picture-1.jpg"
                                                        alt="Neil image"
                                                    />
                                                </div>
                                                <div
                                                    class="flex-1 min-w-0 ms-4"
                                                >
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                                    >
                                                        Neil Sims
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-500 truncate dark:text-gray-400"
                                                    >
                                                        email@windster.com
                                                    </p>
                                                </div>
                                                <div
                                                    class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white"
                                                >
                                                    $320
                                                </div>
                                            </div>
                                        </li>
                                        <li class="py-3 sm:py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <img
                                                        class="w-8 h-8 rounded-full"
                                                        src="/docs/images/people/profile-picture-3.jpg"
                                                        alt="Bonnie image"
                                                    />
                                                </div>
                                                <div
                                                    class="flex-1 min-w-0 ms-4"
                                                >
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                                    >
                                                        Bonnie Green
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-500 truncate dark:text-gray-400"
                                                    >
                                                        email@windster.com
                                                    </p>
                                                </div>
                                                <div
                                                    class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white"
                                                >
                                                    $3467
                                                </div>
                                            </div>
                                        </li>
                                        <li class="py-3 sm:py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <img
                                                        class="w-8 h-8 rounded-full"
                                                        src="/docs/images/people/profile-picture-2.jpg"
                                                        alt="Michael image"
                                                    />
                                                </div>
                                                <div
                                                    class="flex-1 min-w-0 ms-4"
                                                >
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                                    >
                                                        Michael Gough
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-500 truncate dark:text-gray-400"
                                                    >
                                                        email@windster.com
                                                    </p>
                                                </div>
                                                <div
                                                    class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white"
                                                >
                                                    $67
                                                </div>
                                            </div>
                                        </li>
                                        <li class="py-3 sm:py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <img
                                                        class="w-8 h-8 rounded-full"
                                                        src="/docs/images/people/profile-picture-4.jpg"
                                                        alt="Lana image"
                                                    />
                                                </div>
                                                <div
                                                    class="flex-1 min-w-0 ms-4"
                                                >
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                                    >
                                                        Lana Byrd
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-500 truncate dark:text-gray-400"
                                                    >
                                                        email@windster.com
                                                    </p>
                                                </div>
                                                <div
                                                    class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white"
                                                >
                                                    $367
                                                </div>
                                            </div>
                                        </li>
                                        <li class="pt-3 pb-0 sm:pt-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <img
                                                        class="w-8 h-8 rounded-full"
                                                        src="/docs/images/people/profile-picture-5.jpg"
                                                        alt="Thomas image"
                                                    />
                                                </div>
                                                <div
                                                    class="flex-1 min-w-0 ms-4"
                                                >
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                                    >
                                                        Thomes Lean
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-500 truncate dark:text-gray-400"
                                                    >
                                                        email@windster.com
                                                    </p>
                                                </div>
                                                <div
                                                    class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white"
                                                >
                                                    $2367
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
