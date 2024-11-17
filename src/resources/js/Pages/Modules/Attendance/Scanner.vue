<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { QrcodeStream } from "vue3-qrcode-reader";
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const prop = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    user_id: prop.users.id,
    date: "",
});
const result = ref("");

const submit = () => {
    form.post(route("attendance.store"), {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset();
            if (page.props.flash.message) {
                toast.success(page.props.flash.message, {
                    position: toast.POSITION.TOP_RIGHT,
                    autoClose: 2000,
                });
            } else {
                toast.error(page.props.flash.error, {
                    position: toast.POSITION.TOP_RIGHT,
                    autoClose: 2000,
                });
            }
        },
    });
};

const paused = ref(false);
const showScanConfirmation = ref(false);

const onCameraOn = () => {
    showScanConfirmation.value = false;
};

const onCameraOff = () => {
    showScanConfirmation.value = true;
};

const onError = (error) => {
    console.error(error);
};

const onDetect = async (detectedCodes) => {
    try {
        detectedCodes = await detectedCodes;
        result.value = detectedCodes.content;
        form.date = detectedCodes.content;
        await timeout(500);
        submit();
    } catch (error) {
        console.error("Error detecting codes:", error);
        result.value = "Error detecting codes";
    }
};

const timeout = (ms) => {
    return new Promise((resolve) => {
        window.setTimeout(resolve, ms);
    });
};

</script>

<style scoped>
.error {
    font-weight: bold;
    color: red;
}
.barcode-format-checkbox {
    margin-right: 10px;
    white-space: nowrap;
    display: inline-block;
}
.scan-confirmation {
  position: absolute;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  flex-flow: row nowrap;
  justify-content: center;
}
</style>

<template>
    <AppLayout title="Scanner">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Scanner
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-center">
                    <div class="p-8">
                        <div class="w-full justify-center flex">
                            <div>
                                <div class="pb-10 rounded-full">
                                    <qrcode-stream
                                        :paused="paused"
                                        @detect="onDetect"
                                        @camera-on="onCameraOn"
                                        @camera-off="onCameraOff"
                                        @error="onError"
                                    >
                                        <div v-show="showScanConfirmation" class="scan-confirmation">
                                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSQWN-SLzk5eeEuA9zBJKzsM0qbvtLsKDfJ-w&s" alt="Checkmark" width="128" />
                                        </div>
                                    </qrcode-stream>
                                </div>
                                <Link
                                    :href="route('dashboard')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                                >
                                    Close Scanner
                                </Link>
                            </div>
                        </div>
                        <div v-if="error" class="error">{{ error }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>