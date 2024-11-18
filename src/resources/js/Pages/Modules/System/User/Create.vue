<template>
    <AppLayout title="System User">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Create New User
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Use this form to create a new user.
            </p>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg"
                >
                    <form class="p-4 md:p-5" @submit.prevent="submit">
                        <div class="flex items-center w-full gap-3 px-10">
                            <div class="grid gap-4 mb-4 grid-cols-2 w-full">
                                <div class="col-span-2">
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >User Fullname</label
                                    >
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="ex. Juan Dela Cruz"
                                    />
                                    <span class="text-red-600">{{
                                        form.errors.name
                                    }}</span>
                                </div>
                            </div>
                            <div class="grid gap-4 mb-4 grid-cols-2 w-full">
                                <div class="col-span-2">
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >User Email</label
                                    >
                                    <input
                                        type="text"
                                        v-model="form.email"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="ex. jdelacruz@antiquespride.edu.ph"
                                    />
                                    <span class="text-red-600">{{
                                        form.errors.email
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center w-full gap-3 px-10">
                            <div class="grid gap-4 mb-4 grid-cols-2 w-full">
                                <div class="col-span-2">
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >User Role</label
                                    >
                                    <v-select :options="options" v-model="form.roles" multiple>

                                    </v-select>
                                    <span class="text-red-600">{{
                                            form.errors.email
                                        }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="px-10 pt-5">
                            <Link
                                :href="route('system.user')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-4"
                                >Cancel</Link
                            >
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
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
import {computed} from "vue";

const props = defineProps({
    roles: Array,
});

const form = useForm({
    name: "",
    email: "",
    roles: [],
});

const options = computed(() => {
    if (Array.isArray(props.roles) && Array.isArray(props.roles)) {
        return props.roles.map((role) => ({
            label: role.name,
            id: role.id,
        }));
    }
    return [];
});

const submit = () => {
    form.post(route("system.user.store"), {
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
    });
};
</script>
