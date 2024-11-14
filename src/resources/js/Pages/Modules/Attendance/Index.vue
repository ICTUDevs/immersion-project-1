<template>
    <AppLayout title="Daily Time Record">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Daily Time Record
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <div class="relative overflow-x-auto p-5">
                        <div
                            class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4"
                        >
                            <div
                                class="flex flex-row items-center justify-between mb-4"
                            ></div>
                        </div>
                        <table
                            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                        >
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border"
                            >
                                <tr>
                                    <th scope="col" class="px-6 py-3"></th>
                                    <th scope="col" class="px-6 py-3"></th>
                                    <th
                                        scope="col"
                                        colspan="2"
                                        class="px-6 py-3 text-center border"
                                    >
                                        AM
                                    </th>
                                    <th
                                        scope="col"
                                        colspan="2"
                                        class="px-6 py-3 text-center border"
                                    >
                                        PM
                                    </th>
                                    <th
                                        scope="col"
                                        colspan="2"
                                        class="px-6 py-3 text-center border"
                                    >
                                        Undertime / Late
                                    </th>
                                </tr>
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border w-1/4"
                                    >
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 border">
                                        Log Date
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border text-center"
                                    >
                                        Arrival
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border text-center"
                                    >
                                        Departure
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border text-center"
                                    >
                                        Arrival
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border text-center"
                                    >
                                        Departure
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border text-center"
                                    >
                                        Hours
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border text-center"
                                    >
                                        Minutes
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="bg-white border dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                >
                                    <th
                                        scope="row"
                                        class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        <img
                                            class="w-10 h-10 rounded-full"
                                            :src="user.user.profile_photo_url"
                                            :alt="user.user.name"
                                        />
                                        <div class="ps-3">
                                            <div
                                                class="text-base font-semibold"
                                            >
                                                {{ user.user.name }}
                                            </div>
                                        </div>
                                    </th>
                                    <td class="px-6 py-4 border text-center">
                                        {{ user.date }}
                                    </td>
                                    <td class="px-6 py-4 border text-center">
                                        {{ formatTime(user.am_time_in) }}
                                    </td>
                                    <td class="px-6 py-4 border text-center">
                                        {{ formatTime(user.am_time_out) }}
                                    </td>
                                    <td class="px-6 py-4 border text-center">
                                        {{ formatTime(user.pm_time_in) }}
                                    </td>
                                    <td class="px-6 py-4 border text-center">
                                        {{ formatTime(user.pm_time_out) }}
                                    </td>
                                    <td class="px-6 py-4 border text-center">
                                        {{ user.hours_under_time }}
                                    </td>
                                    <td class="px-6 py-4 border text-center">
                                        {{ user.minutes_under_time }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import { format } from "date-fns";

const formatTime = (datetime) => {
    if (!datetime) {
        return "";
    }
    return format(new Date(datetime), "hh:mm:ss a");
};

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const getDate = (date) =>
    new Date(date).toLocaleDateString("en-us", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
</script>
