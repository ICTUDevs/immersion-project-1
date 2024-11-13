<template>
    <AppLayout title="Daily Time Record">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Daily Time Record
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="relative overflow-x-auto p-5">
                        <div
                            class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
                            <div
                                class="flex flex-row items-center justify-between mb-4"
                            >
                                <div>
                                    <!-- Action Dropdown -->
                                    <button
                                        id="dropdownActionButton"
                                        data-dropdown-toggle="dropdownAction"
                                        class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                        type="button"
                                    >
                                        <span class="sr-only"
                                        >Action button</span
                                        >
                                        Action
                                        <svg
                                            class="w-2.5 h-2.5 ms-2.5"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 10 6"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m1 1 4 4 4-4"
                                            />
                                        </svg>
                                    </button>
                                    <!-- Dropdown menu -->
                                    <div
                                        id="dropdownAction"
                                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600"
                                    >
                                        <ul
                                            class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                            aria-labelledby="dropdownActionButton"
                                        >
                                            <li>
                                                <Link
                                                    :href="route('system.role.create')"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                >New Role
                                                </Link>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border">
                            <tr>
                                <th scope="col" class="px-6 py-3">

                                </th>
                                <th scope="col" class="px-6 py-3">

                                </th>
                                <th scope="col" colspan="2" class="px-6 py-3 text-center border">
                                    AM
                                </th>
                                <th scope="col" colspan="2" class="px-6 py-3 text-center border">
                                    PM
                                </th>
                                <th scope="col" colspan="2" class="px-6 py-3 text-center border">
                                    Undertime / Late
                                </th>
                            </tr>
                            <tr>
                                <th scope="col" class="px-6 py-3 border w-1/4">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 border">
                                    Log Date
                                </th>
                                <th scope="col" class="px-6 py-3 border text-center">
                                    Arrival
                                </th>
                                <th scope="col" class="px-6 py-3 border text-center">
                                    Departure
                                </th>
                                <th scope="col" class="px-6 py-3 border text-center">
                                    Arrival
                                </th>
                                <th scope="col" class="px-6 py-3 border text-center">
                                    Departure
                                </th>
                                <th scope="col" class="px-6 py-3 border text-center">
                                    Hours
                                </th>
                                <th scope="col" class="px-6 py-3 border text-center">
                                    Minutes
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                            >
                                <th scope="row"
                                    class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                    <img class="w-10 h-10 rounded-full" :src="user.profile_photo_url" :alt="user.name">
                                    <div class="ps-3">
                                        <div class="text-base font-semibold">{{ user.name }}</div>
                                        <div class="font-normal text-gray-500">{{ user.email }}</div>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    <span v-if="user.roles.length > 0">
                                        <span v-for="role in user.roles">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full"
                                            >
                                                {{ role.name }}
                                            </span>
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ getDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4">
                                    <Link :href="route('system.user.edit', user.hashed_id)"
                                          class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-4">Edit
                                    </Link>
                                    <a href="#" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
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
import AppLayout from '@/Layouts/AppLayout.vue';
import {onMounted} from "vue";
import {initFlowbite} from "flowbite";

onMounted(() => {
    initFlowbite();
});

const props = defineProps(
    {
        users: {
            type: Array,
            required: true
        }
    }
);

const getDate = (date) =>
    new Date(date).toLocaleDateString("en-us", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
</script>
