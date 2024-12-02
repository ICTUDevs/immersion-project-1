<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import "vue3-toastify/dist/index.css";
import { format } from "date-fns";
import { initFlowbite } from "flowbite";
import { usePage } from "@inertiajs/vue3";
import VueQrcode from "@chenfengyuan/vue-qrcode";
import ScannerComponent from "@/Components/Scanner.vue";
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const page = usePage();

onMounted(() => {
    initFlowbite();
});

const prop = defineProps({
    qrcode: Object,
    users: Array,
    user: Array,
    flash: Object,
    userLog: Object,
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
        setExactInterval(countUsersWithTimeIn, 3000);
    }
});

onUnmounted(() => {
    clearInterval(intervalId);
});

const users = ref(prop.users);
const qrcode = ref(prop.qrcode);
const countUser = ref(null);
const showScanner = ref(false);

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

const countUsersWithTimeIn = async () => {
    try {
        const response = await axios.get("attendance/countUsersWithTimeIn");
        countUser.value = response.data;
    } catch (error) {
        console.error("Error counting users with time in:", error);
    }
};

countUsersWithTimeIn();

Echo.private("updates")
    .listen("RefreshUser", (e) => {
        console.log("RefreshUser event received:", e);
        fetchUsers();
        fetchQRCode();
        countUsersWithTimeIn();
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

const form = useForm({
    user_id: prop.userLog.id,
    date: "",
});

const submit = () => {
    form.post(route("attendance.store"), {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset();
            if (prop.flash.message) {
                if (typeof prop.flash.message === "object") {
                    Object.keys(prop.flash.message).forEach((key) => {
                        prop.flash.message[key].forEach((element) => {
                            toast.error(element, {
                                position: toast.POSITION.TOP_RIGHT,
                                autoClose: 2000,
                            });
                        });
                    });
                } else {
                    console.log(prop.flash.message);
                    toast.success(prop.flash.message, {
                        position: toast.POSITION.TOP_RIGHT,
                        autoClose: 2000,
                    });
                }
            } else {
                if (typeof prop.flash.error === "object") {
                    Object.keys(prop.flash.error).forEach((key) => {
                        prop.flash.error[key].forEach((element) => {
                            toast.error(element, {
                                position: toast.POSITION.TOP_RIGHT,
                                autoClose: 2000,
                            });
                        });
                    });
                } else {
                    toast.error(prop.flash.error, {
                        position: toast.POSITION.TOP_RIGHT,
                        autoClose: 2000,
                    });
                }
            }
        },
    });
};

const handleDetected = (content) => {
    form.date = content;
    submit();

    showScanner.value = false;
};

const getDate = (startDate, endDate) => {
    const start = new Date(startDate);
    const end = new Date(endDate);

    // If dates are the same, return single date
    if (startDate === endDate) {
        return start.toLocaleDateString("en-us", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    }

    // If same month and year
    if (
        start.getMonth() === end.getMonth() &&
        start.getFullYear() === end.getFullYear()
    ) {
        return `${start.toLocaleDateString("en-us", {
            month: "long",
        })} ${start.getDate()}-${end.getDate()}, ${start.getFullYear()}`;
    }

    // If different months but same year
    if (start.getFullYear() === end.getFullYear()) {
        return `${start.toLocaleDateString("en-us", {
            month: "long",
        })} ${start.getDate()} - ${end.toLocaleDateString("en-us", {
            month: "long",
        })} ${end.getDate()}, ${start.getFullYear()}`;
    }

    // If different years
    return `${start.toLocaleDateString("en-us", {
        month: "long",
        day: "numeric",
        year: "numeric",
    })} - ${end.toLocaleDateString("en-us", {
        month: "long",
        day: "numeric",
        year: "numeric",
    })}`;
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
                    <div class="w-full">
                        <div
                            v-if="
                                $page.props.isSuperAdmin ||
                                $page.props.isTimeKeeper
                            "
                        >
                            <div
                                class="flex flex-col items-center w-full text-center"
                            >
                                <div
                                    class="w-full flex flex-col sm:flex-row items-center justify-center"
                                >
                                    <div class="px-4 pt-8">
                                        <h1
                                            class="mb-4 text-5xl uppercase font-extrabold leading-none tracking-tight text-gray-900 dark:text-white"
                                        >
                                            Attendance Tracker
                                        </h1>
                                        <p
                                            class="mb-4 text-xl font-normal text-gray-500 dark:text-gray-400"
                                        >
                                            {{ formattedDate }}
                                        </p>
                                        <h1
                                            class="mb-6 text-6xl font-bold text-gray-500 dark:text-gray-400"
                                        >
                                            {{ formattedTime }}
                                        </h1>
                                    </div>
                                    <div class="px-4 pt-8">
                                        <p
                                            class="mb-2 text-1xl font-normal text-gray-500 lg:text-xl dark:text-gray-400"
                                        >
                                            Scan the QR Code to store your login
                                            or logout
                                        </p>
                                        <div class="flex justify-center">
                                            <figure class="qrcode">
                                                <vue-qrcode
                                                    :value="qrcode.qr_code"
                                                    tag="svg"
                                                    :options="{
                                                        errorCorrectionLevel:
                                                            'Q',
                                                        width: 350,
                                                    }"
                                                ></vue-qrcode>
                                                <img
                                                    class="qrcode__image"
                                                    src="ictu.png"
                                                    alt="qrcode"
                                                />
                                            </figure>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-8 pb-8 flex justify-center w-full">
                                <div
                                    class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700"
                                >
                                    <div class="flex items-center gap-4 mb-4">
                                        <h5
                                            class="text-2xl font-bold leading-none text-gray-900 dark:text-white"
                                        >
                                            On Duty:
                                        </h5>
                                        <h5
                                            class="text-2xl font-bold leading-none text-green-600 dark:text-white"
                                        >
                                            {{ countUser }}
                                        </h5>
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center flex-row justify-center gap-3"
                                    >
                                        <div
                                            class="w-1/4 p-5 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                            v-for="(item, index) in users"
                                            :key="item.id"
                                        >
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <img
                                                    class="w-32 h-32 mb-3 rounded-full border"
                                                    :src="
                                                        item.user
                                                            .profile_photo_url
                                                    "
                                                    :alt="item.user.name"
                                                />
                                                <h5
                                                    class="mb-1 text-wrap text-center text-xl font-bold text-gray-900 dark:text-white break-words"
                                                >
                                                    {{ item.user.name }}
                                                </h5>
                                                <p
                                                    class="text-sm overflow-hidden truncate text-center text-gray-500 dark:text-gray-400"
                                                    >{{ item.user.email }}</p
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-8" v-if="$page.props.isOJT">
                        <ScannerComponent
                            v-if="showScanner"
                            @close="showScanner = false"
                            @detected="handleDetected"
                            :users="prop.users"
                        />

                        <div class="w-full justify-center flex pb-10">
                            <div>
                                <button
                                    @click="showScanner = true"
                                    v-if="showScanner === false"
                                    class="inline-flex items-end px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                                >
                                    Open Scanner
                                </button>
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
                                    <div
                                        class="text-xs uppercase font-bold py-2 text-gray-700 dark:text-gray-400"
                                    >
                                        {{ getDate(item.date, item.date) }}
                                    </div>
                                    <div>
                                        <!-- AM Table -->

                                        <table
                                            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                                            v-if="
                                                item.am_time_in != null ||
                                                item.am_time_out != null
                                            "
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
                                                        MORNING
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
                                            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mb-8"
                                            v-if="
                                                item.pm_time_in != null ||
                                                item.pm_time_out != null
                                            "
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
                                                        AFTERNOON
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
                                        <hr class="my-7 bg-slate-400 h-1" />
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
    background-color: transparent !important;
}

.qrcode__image {
    background-color: #fff;
    border: 0.25rem solid #fff;
    border-radius: 6rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.25);
    height: 25%;
    left: 50%;
    overflow: hidden;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 25%;
}
</style>
