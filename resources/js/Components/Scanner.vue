<template>
    <div class="p-8 text-center">
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
                        <div
                            v-show="showScanConfirmation"
                            class="scan-confirmation"
                        >
                            <img src="logo.svg" alt="Checkmark" width="128" />
                        </div>
                    </qrcode-stream>
                </div>
                <button
                    @click="emit('close')"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                >
                    Close Scanner
                </button>
            </div>
        </div>
        <div v-if="error" class="text-red-500 pt-5">{{ error }}</div>
        <div v-if="error" class="text-red-500 pt-5">{{ $page.props.flash.error }}</div>
    </div>
</template>

<script setup>
import { QrcodeStream } from "vue3-qrcode-reader";
import { ref } from "vue";

const emit = defineEmits(["close", "detected"]);

const paused = ref(false);
const showScanConfirmation = ref(false);
const error = ref(null);

const onCameraOn = () => {
    showScanConfirmation.value = false;
};

const onCameraOff = () => {
    showScanConfirmation.value = true;
};

const onError = (err) => {
    console.error(err);
    error.value = err.message;
};

const onDetect = async (detectedCodes) => {
    try {
        const result = await detectedCodes;
        emit("detected", result.content);
    } catch (err) {
        error.value = "Error detecting codes";
    }
};
</script>

<style scoped>
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
