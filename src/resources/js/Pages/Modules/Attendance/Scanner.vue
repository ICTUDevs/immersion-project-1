<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { QrcodeStream } from "vue3-qrcode-reader";
// import { onMounted } from 'vue';
import { ref, computed } from "vue";

const prop = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const result = ref("");

const onDetect = async (detectedCodesPromise) => {
    try {
        const detectedCodes = await detectedCodesPromise;
        result.value = detectedCodes.content;

        window.location.href = `/dashboard`;
    } catch (error) {
        console.error('Error detecting codes:', error);
        result.value = 'Error detecting codes';
    }
}

/*** select camera ***/

const selectedConstraints = ref({ facingMode: "environment" });
const defaultConstraintOptions = [
    { label: "rear camera", constraints: { facingMode: "environment" } },
    { label: "front camera", constraints: { facingMode: "user" } },
];
const constraintOptions = ref(defaultConstraintOptions);

const onCameraReady = async () => {
    // NOTE: on iOS we can't invoke `enumerateDevices` before the user has given
    // camera access permission. `QrcodeStream` internally takes care of
    // requesting the permissions. The `camera-on` event should guarantee that this
    // has happened.
    const devices = await navigator.mediaDevices.enumerateDevices();
    const videoDevices = devices.filter(({ kind }) => kind === "videoinput");

    constraintOptions.value = [
        ...defaultConstraintOptions,
        ...videoDevices.map(({ deviceId, label }) => ({
            label: `${label} (ID: ${deviceId})`,
            constraints: { deviceId },
        })),
    ];

    error.value = "";
}

/*** track functons ***/

const paintOutline = (detectedCodes, ctx) => {
    for (const detectedCode of detectedCodes) {
        const [firstPoint, ...otherPoints] = detectedCode.cornerPoints;

        ctx.strokeStyle = "red";

        ctx.beginPath();
        ctx.moveTo(firstPoint.x, firstPoint.y);
        for (const { x, y } of otherPoints) {
            ctx.lineTo(x, y);
        }
        ctx.lineTo(firstPoint.x, firstPoint.y);
        ctx.closePath();
        ctx.stroke();
    }
}
const paintBoundingBox = (detectedCodes, ctx) => {
    for (const detectedCode of detectedCodes) {
        const {
            boundingBox: { x, y, width, height },
        } = detectedCode;

        ctx.lineWidth = 2;
        ctx.strokeStyle = "#007bff";
        ctx.strokeRect(x, y, width, height);
    }
}
const paintCenterText = (detectedCodes, ctx) => {
    for (const detectedCode of detectedCodes) {
        const { boundingBox, rawValue } = detectedCode;

        const centerX = boundingBox.x + boundingBox.width / 2;
        const centerY = boundingBox.y + boundingBox.height / 2;

        const fontSize = Math.max(
            12,
            (50 * boundingBox.width) / ctx.canvas.width
        );

        ctx.font = `bold ${fontSize}px sans-serif`;
        ctx.textAlign = "center";

        ctx.lineWidth = 3;
        ctx.strokeStyle = "#35495e";
        ctx.strokeText(detectedCode.rawValue, centerX, centerY);

        ctx.fillStyle = "#5cb984";
        ctx.fillText(rawValue, centerX, centerY);
    }
}
const trackFunctionOptions = [
    { text: "nothing (default)", value: undefined },
    { text: "outline", value: paintOutline },
    { text: "centered text", value: paintCenterText },
    { text: "bounding box", value: paintBoundingBox },
];
const trackFunctionSelected = ref(trackFunctionOptions[1]);

/*** barcode formats ***/

const barcodeFormats = ref({
    qr_code: true,
});
const selectedBarcodeFormats = computed(() => {
    return Object.keys(barcodeFormats.value).filter(
        (format) => barcodeFormats.value[format]
    );
});

/*** error handling ***/

const error = ref("");

const onError = (err) => {
    error.value = `[${err.name}]: `;

    if (err.name === "NotAllowedError") {
        error.value += "you need to grant camera access permission";
    } else if (err.name === "NotFoundError") {
        error.value += "no camera on this device";
    } else if (err.name === "NotSupportedError") {
        error.value += "secure context required (HTTPS, localhost)";
    } else if (err.name === "NotReadableError") {
        error.value += "is the camera already in use?";
    } else if (err.name === "OverconstrainedError") {
        error.value += "installed cameras are not suitable";
    } else if (err.name === "StreamApiNotSupportedError") {
        error.value += "Stream API is not supported in this browser";
    } else if (err.name === "InsecureContextError") {
        error.value +=
            "Camera access is only permitted in secure context. Use HTTPS or localhost rather than HTTP.";
    } else {
        error.value += err.message;
    }
}
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
</style>

<template>
    <AppLayout title="Scanner">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Scanner
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-center"
                >
                    <div class="p-8">
                        <div class="w-full justify-center flex">
                            <div>
                                <p class="decode-result">
                                    Last result: <b>{{ result }}</b>
                                </p>

                                <div>
                                    <qrcode-stream
                                        :constraints="selectedConstraints"
                                        :track="trackFunctionSelected.value"
                                        :formats="selectedBarcodeFormats"
                                        @error="onError"
                                        @detect="onDetect"
                                        @camera-on="onCameraReady"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
#qrcode-stream {
    width: 25%;
    height: 25%;
    max-width: 400px;
    max-height: 400px;
}
</style>
