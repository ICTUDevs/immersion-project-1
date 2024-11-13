<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import {Html5Qrcode} from "html5-qrcode";
import {onMounted} from 'vue';


const props = defineProps({
    scannedqrcodes: {
        type: String,
        default: ''
    }
});

let html5Qrcode;
const createscanqrcode = () => {
    html5Qrcode = new Html5Qrcode("qrcode-stream");
    const config = {fps: 10, qrbox: {width: 250, height: 250}};

    html5Qrcode.start({facingMode: "environment"}, config, onScanSucess);
}

const onScanSucess = (decodeResult) => {
    props.scannedqrcodes = decodeResult;
    stopscanning();
    console.log('QR Code:', decodeResult);
    window.location.href = '/';
};

const onInit = (promise) => {
    promise.catch(error => {
        console.error('Error initializing camera:', error);
        alert('Failed to initialize camera');
    });
};

const onError = (error) => {
    console.error('Camera error:', error);
    alert('Camera error: ' + error.message);
};

onMounted(() => {
    createscanqrcode();
});

const stopscanning = () => {
    html5Qrcode.stop();
}
</script>

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
                            <div id="qrcode-stream" class="w-1/2 h-1/2">
                                <!-- <qrcode-stream @decode="onDecode" @init="onInit" @error="onError"/> -->
                                {{ scannedqrcodes }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
