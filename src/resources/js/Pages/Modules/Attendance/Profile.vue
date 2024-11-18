<template>
    <AppLayout title="Daily Time Record">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Daily Time Record:
                <span
                    class="font-bold ms-1 uppercase text-blue-500 dark:text-gray-50"
                    >{{ users.name }}</span
                >
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
                                    <th
                                        scope="col"
                                        colspan="2"
                                        class="px-6 py-3 text-center border"
                                    ></th>
                                </tr>
                                <tr>
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
                                        Hour(s)
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border text-center"
                                    >
                                        Minute(s)
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 border text-center"
                                    >
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="user in users.attendances"
                                    :key="user.id"
                                    class="bg-white border dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                >
                                    <td class="px-6 py-4 border">
                                        {{ getDate(user.date) }}
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
                                    <td class="px-6 py-4 border text-center">
                                        <Link
                                            class="text-blue-600 hover:underline mx-1 dark:text-blue-400 dark:hover:text-blue-600"
                                            :href="
                                                route(
                                                    'attendance.log.edit',
                                                    user.hashed_id
                                                )
                                            "
                                        >
                                            Edit
                                        </Link>
                                        <a
                                            role="button"
                                            class="text-red-600 hover:underline mx-1 dark:text-red-400 dark:hover:text-red-600"
                                            @click="confirmDeletion"
                                        >
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Time Log Confirmation Modal -->
        <DialogModal :show="confirmingDeletion" @close="closeModal">
            <template #title> Delete Time Log </template>

            <template #content>
                Are you sure you want to delete this Time Log?
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                <DangerButton
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="destroy"
                >
                    Delete Time Log
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { onMounted, ref } from "vue";
import { initFlowbite } from "flowbite";
import { format } from "date-fns";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useForm } from "@inertiajs/vue3";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const formatTime = (datetime) => {
    if (!datetime) {
        return "";
    }
    return format(new Date(datetime), "hh:mm a");
};

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    flash: {
        type: Object,
        required: true,
    },
});

const getDate = (date) =>
    new Date(date).toLocaleDateString("en-us", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });

if (props.flash.message !== null) {
    toast.success(props.flash.message, {
        position: toast.POSITION.TOP_RIGHT,
        autoClose: 2000,
    });
}

const confirmingDeletion = ref(false);

const confirmDeletion = () => {
    confirmingDeletion.value = true;
};

const closeModal = () => {
    confirmingDeletion.value = false;
};

const form = useForm({ id: "" });

const destroy = () => {
    form.delete(
        route("attendance.delete", {
            hashedId: props.users.attendances[0].hashed_id,
            hashed_id: props.users.hashed_id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success("Time Log deleted successfully", {
                    position: toast.POSITION.TOP_RIGHT,
                    autoClose: 2000,
                });
            },
        }
    );
};
</script>
