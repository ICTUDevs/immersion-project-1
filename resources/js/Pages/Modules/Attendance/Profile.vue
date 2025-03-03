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
                            class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center pb-4"
                        >
                            <div
                                class="flex flex-col md:flex-row md:items-end w-full mb-1 gap-2"
                            >
                                <div class="w-full md:w-1/4">
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >Select a month</label
                                    >
                                    <v-select
                                        :options="options"
                                        v-model="selectedOption"
                                        class="w-full"
                                    >
                                    </v-select>
                                </div>
                                <DangerButton
                                    class=""
                                    :class="{ 'opacity-25': form.processing }"
                                    @click="generatePdf"
                                >
                                    Generate PDF
                                </DangerButton>
                            </div>
                        </div>

                        <div class="overflow-x-auto w-full">
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
                                            v-if="$page.props.isSuperAdmin"
                                            scope="col"
                                            colspan="2"
                                            class="px-6 py-3 text-center border"
                                        ></th>
                                    </tr>
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 border"
                                        >
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
                                            v-if="$page.props.isSuperAdmin"
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
                                        <td
                                            class="px-6 py-4 border text-center"
                                        >
                                            {{ formatTime(user.am_time_in) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border text-center"
                                        >
                                            {{ formatTime(user.am_time_out) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border text-center"
                                        >
                                            {{ formatTime(user.pm_time_in) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border text-center"
                                        >
                                            {{ formatTime(user.pm_time_out) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border text-center"
                                        >
                                            {{ user.hours_under_time }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border text-center"
                                        >
                                            {{ user.minutes_under_time }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border text-center"
                                            v-if="$page.props.isSuperAdmin"
                                        >
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
import { onMounted, ref, computed } from "vue";
import { initFlowbite } from "flowbite";
import { format } from "date-fns";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useForm } from "@inertiajs/vue3";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { jsPDF } from "jspdf";
import "jspdf-autotable";

const formatTime = (datetime) => {
    if (!datetime) {
        return "";
    }
    return format(new Date(datetime), "hh:mm");
};

const options = computed(() => {
    if (Array.isArray(props.users.attendances)) {
        const uniqueMonths = new Set(
            props.users.attendances.map((dtr) => dtr.date.slice(0, 7))
        );

        return Array.from(uniqueMonths).map((month) => ({
            label: format(new Date(month + "-01"), "MMMM yyyy").toUpperCase(),
            id: month,
        }));
    }
    return [];
});

const selectedOption = ref(null);

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    flash: {
        type: Object,
        required: true,
    },
    logo: {
        type: String,
        required: true,
        default: "/logo.png",
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

const generatePdf = async () => {
    if (!selectedOption.value || !selectedOption.value.id) {
        toast.error("Please select a month to generate the PDF");
        return;
    }

    const selectedMonth = selectedOption.value.id;


    const columns = [
        { dataKey: "name", title: "Name", text: "center" },
        {
            dataKey: "am_time_in",
            title: "Arrival",
            styles: { halign: "center" },
        },
        {
            dataKey: "am_time_out",
            title: "Departure",
            styles: { halign: "center" },
        },
        {
            dataKey: "pm_time_in",
            title: "Arrival",
            styles: { halign: "center" },
        },
        {
            dataKey: "pm_time_out",
            title: "Departure",
            styles: { halign: "center" },
        },
        {
            dataKey: "hours_under_time",
            title: "Hour(s)",
            styles: { halign: "center" },
        },
        {
            dataKey: "minutes_under_time",
            title: "Minute(s)",
            styles: { halign: "center" },
        },
        {
            dataKey: "remarks",
            title: "Remarks",
            styles: { halign: "center" },
        },
    ];

    const head = [
        [
            {
                content: "Day",
                colSpan: 1,
                rowSpan: 2,
                styles: {
                    halign: "center",
                    valign: "middle",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "A.M.",
                colSpan: 2,
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "P.M.",
                colSpan: 2,
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "Undertime / Late",
                colSpan: 2,
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "Remarks",
                colSpan: 1,
                rowSpan: 2,
                styles: {
                    halign: "center",
                    valign: "middle",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
        ],
        [
            {
                content: "Arrival",
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "Departure",
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "Arrival",
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "Departure",
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "Hour(s)",
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
            {
                content: "Minute(s)",
                styles: {
                    halign: "center",
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 6,
                },
            },
        ],
    ];

    const doc = new jsPDF({
        orientation: "portrait",
        unit: "in",
        format: "a4",
    });

    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 0.1; // Margin on each side of the page
    const spacing = 0.0001; // Spacing between the tables
    const tableWidth = (pageWidth - 2 * margin - spacing) / 2; // Adjust the width of each table to fit with spacing

    const userName = `${props.users.name.toUpperCase()}`;

    const addHeader = (startX, startY) => {
        doc.setFont("helvetica", "bold")
            .setFontSize(12)
            .setTextColor(0, 0, 0)
            .text(
                "DAILY TIME RECORD",
                startX +
                    tableWidth / 2 -
                    doc.getTextWidth("DAILY TIME RECORD") / 2,
                startY
            );

        doc.setFont("helvetica", "normal")
            .setFontSize(8)
            .setTextColor(0, 0, 0)
            .text(
                "-----oOo-----",
                startX + tableWidth / 2 - doc.getTextWidth("-----oOo-----") / 2,
                startY + 0.2
            );

        doc.setTextColor(0, 0, 0)
            .setFontSize(9)
            .text(
                userName,
                startX + tableWidth / 2 - doc.getTextWidth(userName) / 2,
                startY + 0.4
            );

        doc.setDrawColor(150, 150, 150);
        doc.setLineWidth(0.01).line(
            startX + 0.1,
            startY + 0.48,
            startX + tableWidth - 0.1,
            startY + 0.48
        );

        doc.setTextColor(0, 0, 0)
            .setFontSize(8)
            .text(
                "NAME",
                startX + tableWidth / 2 - doc.getTextWidth("NAME") / 2,
                startY + 0.6
            );

        doc.setTextColor(0, 0, 0)
            .setFontSize(8)
            .text(
                "For the month of  " +
                    format(
                        new Date(selectedMonth),
                        "MMMM yyyy"
                    ).toUpperCase(),
                startX + 0.09, // Adjust the margin as needed
                startY + 0.9,
                { align: "left" }
            );

        doc.setDrawColor(150, 150, 150);
        doc.setLineWidth(0.01).line(
            startX + 1,
            startY + 0.98,
            startX + tableWidth - 0.1,
            startY + 0.98
        );
    };

    const getDaysInMonth = (dateString) => {
        const [year, month] = dateString.split("-").map(Number);
        return new Date(year, month, 0).getDate();
    };

    const holidays = [
        { date: "01-01", name: "New Year's Day" },
        { date: "02-25", name: "EDSA Revolution" },
        { date: "04-09", name: "Araw ng Kagitingan" },
        { date: "05-01", name: "Labor Day" },
        { date: "06-12", name: "Independence Day" },
        { date: "08-23", name: "Ninoy Aquino Day" },
        { date: "08-28", name: "National Heroes Day" },
        { date: "11-01", name: "All Saints' Day" },
        { date: "11-02", name: "All Souls Day" },
        { date: "11-30", name: "Bonifacio Day" },
        { date: "12-8", name: "Feast of the Immaculate Conception of the Blessed Virgin Mary" },
        { date: "12-25", name: "Christmas Day" },
        { date: "12-30", name: "Rizal Day" },
    ];

    const generateTable = (startX, startY) => {
        const userName = `${props.users.name.toUpperCase()}`;
        const monthYear = format(
            new Date(selectedMonth),
            "MMMM yyyy"
        ).toUpperCase();

        addHeader(startX, startY);

        const daysInMonth = getDaysInMonth(selectedMonth);
        const attendanceMap = new Map(
            props.users.attendances
                .filter((item) => item.date.startsWith(selectedMonth))
                .map((item) => [new Date(item.date).getDate(), item])
        );

        const body = [];
        let totalHoursUnderTime = 0;
        let totalMinutesUnderTime = 0;

        for (let day = 1; day <= daysInMonth; day++) {
            const item = attendanceMap.get(day) || {};
            const hoursUnderTime = parseFloat(item.hours_under_time) || 0;
            const minutesUnderTime = parseFloat(item.minutes_under_time) || 0;

            totalHoursUnderTime += hoursUnderTime;
            totalMinutesUnderTime += minutesUnderTime;

            const date = new Date(
                selectedMonth + "-" + String(day).padStart(2, "0")
            );
            const dayOfWeek = date.getDay();
            let remarks = item.remarks || "";

            if (dayOfWeek === 6) {
                remarks = "Saturday";
            } else if (dayOfWeek === 0) {
                remarks = "Sunday";
            }

            // Check if the date is a holiday
            const formattedDate = format(date, "MM-dd");
            const holiday = holidays.find(
                (holiday) => holiday.date === formattedDate
            );
            if (holiday) {
                remarks = "Holiday";
            }

            body.push({
                name: {
                    content: day.toString(),
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: null,
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
                am_time_in: {
                    content: formatTime(item.am_time_in || ""),
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: null,
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
                am_time_out: {
                    content: formatTime(item.am_time_out || ""),
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: null,
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
                pm_time_in: {
                    content: formatTime(item.pm_time_in || ""),
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: null,
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
                pm_time_out: {
                    content: formatTime(item.pm_time_out || ""),
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: null,
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
                hours_under_time: {
                    content: item.hours_under_time || "",
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: null,
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
                minutes_under_time: {
                    content: item.minutes_under_time || "",
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: null,
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
                remarks: {
                    content: remarks,
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: null,
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
            });
        }

        // Convert total minutes to hours if they exceed 60
        totalHoursUnderTime += Math.floor(totalMinutesUnderTime / 60);
        totalMinutesUnderTime = totalMinutesUnderTime % 60;

        // Add total row
        body.push({
            name: {
                content: "Total",
                styles: {
                    halign: "right",
                    fontSize: 8,
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    cellPadding: 0.05,
                    valign: "middle",
                },
                colSpan: 5, // Merge the first 5 columns
            },
            hours_under_time: {
                content: totalHoursUnderTime.toString(),
                styles: {
                    halign: "center",
                    fontSize: 8,
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [150, 150, 150],
                    lineWidth: 0.001,
                    cellPadding: 0.05,
                    valign: "middle",
                },
            },
            minutes_under_time: {
                content: totalMinutesUnderTime.toString(),
                styles: {
                    halign: "center",
                    fontSize: 8,
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [150, 150, 150],
                    lineWidth: 0.001,
                    cellPadding: 0.05,
                    valign: "middle",
                },
            },
            remarks: {
                content: "",
                styles: {
                    halign: "center",
                    fontSize: 8,
                    fillColor: null,
                    lineColor: 0.9,
                    textColor: [150, 150, 150],
                    lineWidth: 0.001,
                    cellPadding: 0.05,
                    valign: "middle",
                },
            },
        });

        const footer = function (data) {
            const text = [
                "I certify on my honor that the above is a true and correct report of the hours of work performed,",
                "record of which was made daily at the time of arrival and departure from office.",
            ];
            doc.setFont("helvetica", "italic")
                .setFontSize(6)
                .text(
                    text,
                    data.settings.margin.left,
                    doc.internal.pageSize.height - 2.1
                );

            doc.setTextColor(0, 0, 0)
                .setFont("helvetica", "normal")
                .setFontSize(9)
                .text(
                    `${userName}`,
                    startX +
                        tableWidth / 2 -
                        doc.getTextWidth(`${userName}`) / 2,
                    doc.internal.pageSize.height - 1.6
                );

            // Add a line below the user's name
            doc.setDrawColor(150, 150, 150);
            doc.setLineWidth(0.01).line(
                startX + 0.1,
                doc.internal.pageSize.height - 1.5,
                startX + tableWidth - 0.1,
                doc.internal.pageSize.height - 1.5
            );

            doc.setTextColor(150, 150, 150).setFont("helvetica", "italic")
                .setFontSize(6)
                .text(
                    "VERIFIED as to the prescribed office hours :",
                    startX +
                        tableWidth / 2 -
                        doc.getTextWidth(
                            "VERIFIED as to the prescribed office hours :"
                        ) /
                            2,
                    doc.internal.pageSize.height - 1.4
                );

            doc.setLineWidth(0.01).line(
                startX + 0.1,
                doc.internal.pageSize.height - 1.2,
                startX + tableWidth - 0.1,
                doc.internal.pageSize.height - 1.2
            );
        };

        doc.autoTable({
            head: head,
            columns: columns,
            body: body,
            didDrawPage: function (data) {
                if (data.pageNumber > 1) {
                    addHeader(doc, startX, startY, userName, monthYear);
                }
            },
            startY: startY + 1.1,
            margin: { left: startX + 0.09, top: 2 }, // Adjust the margin as needed
            tableWidth: tableWidth - 0.2, // Adjust the table width to fit within the calculated width
            didDrawCell: function (data) {
                if (data.row.index === data.table.body.length - 1) {
                    footer(data);
                }
            },
        });
    };

    // Add watermark to the first table
    doc.setFontSize(40);
    doc.setTextColor("#ebecec");
    doc.text("SYSTEM-GENERATED", doc.internal.pageSize.getWidth() / 2.3, 8.1, {
        angle: 60,
        align: "center",
    });

    // Generate the first table for the user
    generateTable(margin, 0.5, props.users);

    // Add watermark to the second table
    doc.setFontSize(40);
    doc.setTextColor("#ebecec");
    doc.text(
        "SYSTEM-GENERATED",
        doc.internal.pageSize.getWidth() / 2.3 + tableWidth + spacing,
        8.1,
        {
            angle: 60,
            align: "center",
        }
    );

    // Generate the second table for the user
    generateTable(margin + tableWidth + spacing, 0.5);

    // Save the PDF with the user's name
    doc.save(`${props.users.name}.pdf`);
};
</script>
