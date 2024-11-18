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
                    <div class="overflow-x-auto p-5">
                        <div
                            class="flex flex-row items-center justify-end mb-4"
                        >
                            <div class="relative w-full">
                                <div
                                    class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none"
                                >
                                    <svg
                                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        aria-hidden="true"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    id="table-search"
                                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search for items"
                                />
                            </div>
                        </div>
                        <table
                            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                        >
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border"
                            >
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border w-1/4"
                                    >
                                        Name
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                v-for="(item, index) in users.data" :key="item.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                >
                                    <td class="px-6 py-4">
                                        <Link
                                        :href="route('attendance.profile', item.hashed_id)"
                                        class="text-blue-600 hover:underline"
                                        >
                                            {{ item.name }}
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                          <!-- Pagination -->
                          <div
                            class="flex flex-row justify-between items-center mt-5 p-4 border rounded-sm"
                        >
                            <!-- Help text -->
                            <span
                                class="text-sm text-gray-700 dark:text-gray-400"
                            >
                                Showing
                                <span
                                    class="font-semibold text-gray-900 dark:text-white"
                                    >{{ users.from }}</span
                                >
                                to
                                <span
                                    class="font-semibold text-gray-900 dark:text-white"
                                    >{{ users.to }}</span
                                >
                                of
                                <span
                                    class="font-semibold text-gray-900 dark:text-white"
                                    >{{ users.total }}</span
                                >
                                Entries
                            </span>

                            <nav aria-label="Page navigation example">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <Link
                                        v-for="link in users.links"
                                        :key="link.label"
                                        :href="link.url"
                                        v-html="link.label"
                                        :class="{
                                            'text-slate-300 hover:text-slate-300 hover:bg-transparent pointer-events-none':
                                                !link.url,
                                            'text-blue-600 font-medium':
                                                link.active,
                                        }"
                                        class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                    ></Link>
                                </ul>
                            </nav>
                        </div>
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
