<template>
    <AppLayout title="System Role">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Permissions for: {{ role.name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Use this form to assign a permission to this role.
            </p>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <form class="p-4 md:p-5" @submit.prevent="submit">
                        <div class="grid grid-cols-4 gap-2 border rounded p-4">
                            <div
                                v-for="permission in permissions"
                                :key="permission.id"
                            >
                                <div class="flex flex-row items-center gap-2">
                                    <input
                                        type="checkbox"
                                        :value="permission.id"
                                        :checked="isPermissionChecked(permission.id)"
                                        v-model="form.permission_id"
                                    />
                                    <label class="text-gray-800 dark:text-gray-200">{{ permission.name }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="pt-5">
                            <Link
                                :href="route('system.role')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4"
                            >CANCEL
                            </Link
                            >
                            <PrimaryButton
                                type="submit"
                                :disabled="form.processing"
                            >
                                Update
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {toast} from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import {useForm, Link} from "@inertiajs/vue3";

const props = defineProps(
    {
        role: Object,
        permissions: Array,
        hashedId: String,
    },
);

const rolePermissions = props.role.permissions.map(permission => permission.id);

const form = useForm({
    permission_id: rolePermissions,
});

const isPermissionChecked = (permissionId) => {
    return rolePermissions.includes(permissionId);
};

const submit = () => {
    form.put(route("system.role.permission.update", props.hashedId), {
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
