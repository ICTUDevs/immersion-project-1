<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import "vue3-toastify/dist/index.css";
import { format } from "date-fns";
import { initFlowbite } from "flowbite";
import { usePage } from "@inertiajs/vue3";
import VueQrcode from "@chenfengyuan/vue-qrcode";

const page = usePage();

onMounted(() => {
    initFlowbite();
});

const prop = defineProps({
    qrcode: Array,
    users: Array,
    user: Array,
});

const currentDate = ref(new Date());
const formattedDate = ref(
    currentDate.value.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    })
);
const formattedTime = ref(
    currentDate.value.toLocaleTimeString("en-US", {
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
        hour12: true,
    })
);

const updateDateTime = () => {
    currentDate.value = new Date();
    formattedDate.value = currentDate.value.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
    formattedTime.value = currentDate.value.toLocaleTimeString("en-US", {
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
        hour12: true,
    });
};

let intervalId;

const setExactInterval = (callback, interval) => {
    const now = new Date();
    const delay = interval - (now % interval);
    setTimeout(() => {
        callback();
        setInterval(callback, interval);
    }, delay);
};

onMounted(() => {
    intervalId = setInterval(updateDateTime, 1000);
    if (page.props.isSuperAdmin || page.props.isTimeKeeper) {
        setExactInterval(fetchQRCode, 20000);
        setExactInterval(fetchUsers, 3000);
    }
});

onUnmounted(() => {
    clearInterval(intervalId);
});

const users = ref(prop.users);
const qrcode = ref(prop.qrcode);

const fetchUsers = async () => {
    try {
        const response = await axios.get("attendance/fetchUser");
        users.value = response.data;
    } catch (error) {
        console.error("Error fetching users:", error);
    }
};

const fetchQRCode = async () => {
    try {
        const response = await axios.get("attendance/fetchQRCode");
        qrcode.value = response.data;
    } catch (error) {
        console.error("Error fetching QR Code:", error);
    }
};

Echo.private("updates")
    .listen("RefreshUser", (e) => {
        console.log("RefreshUser event received:", e);
        fetchUsers();
    })
    .error((error) => {
        console.error("Error listening to channel:", error);
    });

const formatTime = (datetime) => {
    if (!datetime) {
        return "";
    }
    return format(new Date(datetime), "hh:mm a");
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <div
                        class="lg:flex lg:flex-row lg:gap-2 w-full text-center md:flex md:flex-col md:gap-0"
                        v-if="
                            $page.props.isSuperAdmin || $page.props.isTimeKeeper
                        "
                    >
                        <div class="p-8 w-full">
                            <h1
                                class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 dark:text-white"
                            >
                                Attendance Tracker
                            </h1>
                            <p
                                class="mb-4 text-xl font-normal text-gray-500 dark:text-gray-400"
                            >
                                {{ formattedDate }}
                            </p>
                            <h1
                                class="mb-6 text-7xl font-bold text-gray-500 dark:text-gray-400"
                            >
                                {{ formattedTime }}
                            </h1>
                            <p
                                class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400"
                            >
                                Scan the QR Code to store your login or logout
                            </p>
                            <div class="flex justify-center">
                                <figure class="qrcode">
                                    <vue-qrcode
                                        :value="qrcode.qr_code"
                                        tag="svg"
                                        :options="{
                                            errorCorrectionLevel: 'Q',
                                            width: 400,
                                        }"
                                    ></vue-qrcode>
                                    <img
                                        class="qrcode__image"
                                        src="ictu.png"
                                        alt="Chen Fengyuan"
                                    />
                                </figure>
                            </div>
                        </div>
                        <div class="p-8 flex justify-center w-full">
                            <div
                                class="w-full max-w-md p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700"
                            >
                                <div
                                    class="flex items-center justify-between mb-4"
                                >
                                    <h5
                                        class="text-xl font-bold leading-none text-gray-900 dark:text-white"
                                    >
                                        On Duty
                                    </h5>
                                </div>
                                <div class="flow-root">
                                    <ul
                                        role="list"
                                        class="divide-y divide-gray-200 dark:divide-gray-700"
                                        v-for="(item, index) in users"
                                        :key="item.id"
                                    >
                                        <li class="py-3 sm:py-4">
                                            <div
                                                class="flex items-center gap-2 border p-2 rounded-lg justify-between"
                                            >
                                                <div
                                                    class="flex flex-column items-center gap-2 min-w-0"
                                                >
                                                    <img
                                                        class="w-8 h-8 rounded-full"
                                                        :src="
                                                            item.user
                                                                .profile_photo_url
                                                        "
                                                        alt="Neil image"
                                                    />

                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                                    >
                                                        {{ item.user.name }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="flex flex-column min-w-0 ms-4"
                                                >
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                                    >
                                                        {{
                                                            formatTime(
                                                                item.updated_at
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-8" v-if="$page.props.isOJT">
                        <div class="w-full justify-center flex pb-10">
                            <div>
                                <Link
                                    :href="route('attendance.scanner')"
                                    class="inline-flex items-end px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                                >
                                    Open Scanner
                                </Link>
                            </div>
                        </div>
                        <div
                            class="max-h-96 overflow-y-auto"
                            v-if="$page.props.isOJT"
                        >
                            <div>
                                <div
                                    class="relative overflow-y-auto"
                                    v-for="(item, index) in user"
                                    :key="item.id"
                                >
                                    <div>
                                        <!-- AM Table -->
                                        <table
                                            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                                        >
                                            <thead
                                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border"
                                            >
                                                <tr>
                                                    <th
                                                        scope="col"
                                                        colspan="2"
                                                        class="px-6 py-3 text-center border"
                                                    >
                                                        AM - {{ item.date }}
                                                    </th>
                                                </tr>
                                                <tr>
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    class="bg-white border dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                                >
                                                    <td
                                                        class="px-6 py-4 border text-center"
                                                    >
                                                        <span
                                                            v-if="
                                                                item.am_time_in !=
                                                                null
                                                            "
                                                        >
                                                            {{
                                                                formatTime(
                                                                    item.am_time_in
                                                                )
                                                            }}
                                                        </span>
                                                        <span v-else>
                                                            -- -- --
                                                        </span>
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 border text-center"
                                                    >
                                                        <span
                                                            v-if="
                                                                item.am_time_out !=
                                                                null
                                                            "
                                                        >
                                                            {{
                                                                formatTime(
                                                                    item.am_time_out
                                                                )
                                                            }}
                                                        </span>
                                                        <span v-else>
                                                            -- -- --
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <!-- PM Table -->
                                        <table
                                            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-8"
                                        >
                                            <thead
                                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border"
                                            >
                                                <tr>
                                                    <th
                                                        scope="col"
                                                        colspan="2"
                                                        class="px-6 py-3 text-center border"
                                                    >
                                                        PM - {{ item.date }}
                                                    </th>
                                                </tr>
                                                <tr>
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    class="bg-white border dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                                >
                                                    <td
                                                        class="px-6 py-4 border text-center"
                                                    >
                                                        <span
                                                            v-if="
                                                                item.pm_time_in !=
                                                                null
                                                            "
                                                        >
                                                            {{
                                                                formatTime(
                                                                    item.pm_time_in
                                                                )
                                                            }}
                                                        </span>
                                                        <span v-else>
                                                            -- -- --
                                                        </span>
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 border text-center"
                                                    >
                                                        <span
                                                            v-if="
                                                                item.pm_time_out !=
                                                                null
                                                            "
                                                        >
                                                            {{
                                                                formatTime(
                                                                    item.pm_time_out
                                                                )
                                                            }}
                                                        </span>
                                                        <span v-else>
                                                            -- -- --
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
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
.qrcode {
  display: inline-block;
  font-size: 0;
  margin-bottom: 0;
  position: relative;
}

.qrcode__image {
  background-color: #fff;
  border: 0.25rem solid #fff;
  border-radius: 6rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.25);
  height: 30%;
  left: 50%;
  overflow: hidden;
  position: absolute;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 30%;
}
</style>