<template>
    <AppLayout title="Manage Logs">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Modify <span class="font-bold">{{ props.dtr.user.name }}</span
                >'s Logs
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Use this form to modify user logs.
            </p>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <form class="p-4 md:p-5" @submit.prevent="submit">
                        <div class="flex items-center w-full gap-3 px-10">
                            <div class="grid gap-4 mb-4 grid-cols-2 w-full">
                                <div class="col-span-2">
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >AM Arrival</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                        <input
                                            type="time"
                                            v-model="formattedAmTimeIn"
                                            class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        @click="resetAmTimeIn"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        Reset
                                    </button>
                                    <span class="text-red-600">{{
                                        form.errors.am_time_in
                                    }}</span>
                                </div>
                            </div>
                            <div class="grid gap-4 mb-4 grid-cols-2 w-full">
                                <div class="col-span-2">
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >AM Departure</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                        <input
                                            type="time"
                                            v-model="formattedAmTimeOut"
                                            class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        @click="resetAmTimeOut"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        Reset
                                    </button>
                                    <span class="text-red-600">{{
                                        form.errors.am_time_out
                                    }}</span>
                                </div>
                            </div>
                            <div class="grid gap-4 mb-4 grid-cols-2 w-full">
                                <div class="col-span-2">
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >PM Arrival</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                        <input
                                            type="time"
                                            v-model="formattedPmTimeIn"
                                            class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        @click="resetPmTimeIn"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        Reset
                                    </button>
                                    <span class="text-red-600">{{
                                        form.errors.pm_time_in
                                    }}</span>
                                </div>
                            </div>
                            <div class="grid gap-4 mb-4 grid-cols-2 w-full">
                                <div class="col-span-2">
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >PM Departure</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                        <input
                                            type="time"
                                            v-model="formattedPmTimeOut"
                                            class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        @click="resetPmTimeOut"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        Reset
                                    </button>
                                    <span class="text-red-600">{{
                                        form.errors.pm_time_out
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="px-10 pt-5">
                            <Link
                                :href="
                                    route(
                                        'attendance.profile',
                                        props.dtr.hashed_id
                                    )
                                "
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4"
                                >Cancel</Link
                            >
                            <button
                                type="submit"
                                class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                :disabled="form.processing"
                            >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useForm, Link } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { initFlowbite } from "flowbite";
import { DateTime } from "luxon";

const props = defineProps({
    dtr: Object,
    hashedId: String,
});

onMounted(() => {
    initFlowbite();
});

const form = useForm({
    am_time_in: props.dtr.am_time_in,
    am_time_out: props.dtr.am_time_out,
    pm_time_in: props.dtr.pm_time_in,
    pm_time_out: props.dtr.pm_time_out,
});

const formatTime = (dateString) => {
    const date = DateTime.fromISO(dateString, { zone: "Asia/Manila" });
    return date.toFormat("HH:mm");
};

const parseTime = (timeString, originalDateString) => {
    const [hours, minutes] = timeString.split(":");
    let date = DateTime.now().setZone("Asia/Manila");
    originalDateString = date.set({
        hour: parseInt(hours, 10),
        minute: parseInt(minutes, 10),
        second: 0,
        millisecond: 0,
    });
    return originalDateString.toISOTime();
};

const formattedAmTimeIn = computed({
    get() {
        return form.am_time_in ? formatTime(form.am_time_in) : '';
    },
    set(value) {
        form.am_time_in = parseTime(value, form.am_time_in);
    },
});

const formattedAmTimeOut = computed({
    get() {
        return form.am_time_out ? formatTime(form.am_time_out) : '';
    },
    set(value) {
        form.am_time_out = parseTime(value, form.am_time_out);
    },
});

const formattedPmTimeIn = computed({
    get() {
        return form.pm_time_in ? formatTime(form.pm_time_in) : '';
    },
    set(value) {
        form.pm_time_in = parseTime(value, form.pm_time_in);
    },
});

const formattedPmTimeOut = computed({
    get() {
        return form.pm_time_out ? formatTime(form.pm_time_out) : '';
    },
    set(value) {
        form.pm_time_out = parseTime(value, form.pm_time_out);
    },
});

const resetAmTimeIn = () => {
    form.am_time_in = null;
};

const resetAmTimeOut = () => {
    form.am_time_out = null;
};

const resetPmTimeIn = () => {
    form.pm_time_in = null;
};

const resetPmTimeOut = () => {
    form.pm_time_out = null;
};

const submit = () => {
    // Check if any changes were made before submitting
    if (
        form.am_time_in === props.dtr.am_time_in &&
        form.am_time_out === props.dtr.am_time_out &&
        form.pm_time_in === props.dtr.pm_time_in &&
        form.pm_time_out === props.dtr.pm_time_out
    ) {
        toast.info("No changes detected.", {
            position: toast.POSITION.TOP_RIGHT,
            autoClose: 2000,
            onClose: () => {
                window.location.href = `/attendance/profile/${props.dtr.hashed_id}`;
            },
        });
        return;
    }

    form.am_time_in = formattedAmTimeIn;
    form.am_time_out = formattedAmTimeOut;
    form.pm_time_in = formattedPmTimeIn;
    form.pm_time_out = formattedPmTimeOut;
    
    form.put(
        route("attendance.log.update", {
            hashedId: props.hashedId,
            hashed_id: props.dtr.hashed_id,
        }),
        {
            preserveScroll: true,
            onSuccess: (page) => {
                form.reset();
                if (page.props.flash.message) {
                    toast.success(page.props.flash.message, {
                        position: toast.POSITION.TOP_RIGHT,
                        autoClose: 2000,
                    });
                }
            },
        }
    );
};
</script>
