<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {QrcodeStream} from 'vue-qrcode-reader';
import axios from 'axios';

const props = defineProps({
    qrcode: Array,
})

const onDecode = async (result) => {
    try {
        await axios.post('/api/attendance', {code: result});
        alert('Attendance recorded successfully');
    } catch (error) {
        console.error('Error recording attendance:', error);
        alert('Failed to record attendance');
    }
};
</script>

<template>
    <AppLayout title="Scanner">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">

            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-center">
                    <div class="p-8">
                        <div class="w-full justify-center flex">

                            <div class="w-1/2 h-1/2 ">
                                <QrcodeStream @decode="onDecode"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
