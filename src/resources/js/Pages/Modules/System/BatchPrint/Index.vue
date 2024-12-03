<template>
    <AppLayout title="Daily Time Record">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Daily Time Record:
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <div class="relative overflow-x-auto p-5">
                        <div
                            class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4"
                        >
                            <div
                                class="flex flex-row items-center w-full mb-1 gap-2"
                            >
                                <v-select
                                    :options="options"
                                    v-model="selectedOption"
                                    class="w-1/4"
                                >
                                </v-select>
                                <DangerButton
                                    class=""
                                    @click="generatePdf(selectedOption)"
                                >
                                    Generate PDF
                                </DangerButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { onMounted, computed, ref } from "vue";
import { initFlowbite } from "flowbite";
import { format } from "date-fns";
import "vue3-toastify/dist/index.css";
import DangerButton from "@/Components/DangerButton.vue";
import { jsPDF } from "jspdf";
import "jspdf-autotable";

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    flash: {
        type: Object,
        required: true,
    },
});

const formatTime = (datetime) => {
    if (!datetime) {
        return "";
    }
    return format(new Date(datetime), "hh:mm");
};

console.log(props.users[0].attendances[0]);

const options = computed(() => {
    if (
        Array.isArray(props.users) &&
        Array.isArray(props.users[0].attendances)
    ) {
        return props.users[0].attendances.map((dtr) => ({
            label: format(new Date(dtr.date), "MMMM yyyy").toUpperCase(),
            id: dtr.id,
            date: dtr.date,
        }));
    }
    return [];
});

const selectedOption = ref(null);

onMounted(() => {
    initFlowbite();
});

const generatePdf = async (selectedOption) => {
    if (!selectedOption || !selectedOption.date) {
        console.error("No option selected or date is missing");
        return;
    }

    const selectedMonth = selectedOption.date.slice(0, 7);

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
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 8,
                },
            },
            {
                content: "A.M.",
                colSpan: 2,
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 8,
                },
            },
            {
                content: "P.M.",
                colSpan: 2,
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 8,
                },
            },
            {
                content: "Undertime / Late",
                colSpan: 2,
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 8,
                },
            },
        ],
        [
            {
                content: "Arrival",
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 7,
                },
            },
            {
                content: "Departure",
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 7,
                },
            },
            {
                content: "Arrival",
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 7,
                },
            },
            {
                content: "Departure",
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 7,
                },
            },
            {
                content: "Hour(s)",
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 7,
                },
            },
            {
                content: "Minute(s)",
                styles: {
                    halign: "center",
                    fillColor: [255, 255, 255],
                    lineColor: 0.9,
                    textColor: [0, 0, 0],
                    lineWidth: 0.001,
                    fontSize: 7,
                },
            },
        ],
    ];

    const addHeader = (
        doc,
        startX,
        startY,
        userName,
        monthYear,
        tableWidth
    ) => {
        doc.setFont("helvetica", "bold")
            .setFontSize(12)
            .text(
                "DAILY TIME RECORD",
                startX +
                    tableWidth / 2 -
                    doc.getTextWidth("DAILY TIME RECORD") / 2,
                startY
            );

        doc.setFont("helvetica", "normal")
            .setFontSize(8)
            .text(
                "-----oOo-----",
                startX + tableWidth / 2 - doc.getTextWidth("-----oOo-----") / 2,
                startY + 0.2
            );

        doc.setFontSize(9).text(
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

        doc.setFontSize(8).text(
            "NAME",
            startX + tableWidth / 2 - doc.getTextWidth("NAME") / 2,
            startY + 0.6
        );

        doc.setFontSize(8).text(
            "For the month of  " + monthYear,
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

    const generateTable = (doc, startX, startY, user, tableWidth) => {
        const userName = `${user.name.toUpperCase()}`;
        const monthYear = format(
            new Date(selectedMonth),
            "MMMM yyyy"
        ).toUpperCase();

        addHeader(doc, startX, startY, userName, monthYear, tableWidth);

        const daysInMonth = getDaysInMonth(selectedMonth);
        const attendanceMap = new Map(
            user.attendances
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

            body.push({
                name: {
                    content: day.toString(),
                    styles: {
                        halign: "center",
                        fontSize: 8,
                        fillColor: [255, 255, 255],
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
                        fillColor: [255, 255, 255],
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
                        fillColor: [255, 255, 255],
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
                        fillColor: [255, 255, 255],
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
                        fillColor: [255, 255, 255],
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
                        fillColor: [255, 255, 255],
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
                        fillColor: [255, 255, 255],
                        lineColor: 0.9,
                        textColor: [150, 150, 150],
                        lineWidth: 0.001,
                        cellPadding: 0.05,
                        valign: "middle",
                    },
                },
            });
        }

        // Add total row
        body.push({
            name: {
                content: "Total",
                styles: {
                    halign: "right",
                    fontSize: 8,
                    fillColor: [255, 255, 255],
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
                    fillColor: [255, 255, 255],
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
                    fillColor: [255, 255, 255],
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

            doc.setFont("helvetica", "normal")
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

            doc.setFont("helvetica", "italic")
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

    // Loop through each user and generate the PDF
    for (const user of props.users) {
        // Create a new document for each user
        const doc = new jsPDF({
            orientation: "portrait",
            unit: "in",
            format: "a4",
        });

        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 0.1; // Margin on each side of the page
        const spacing = 0.0001; // Spacing between the tables
        const tableWidth = (pageWidth - 2 * margin - spacing) / 2; // Adjust the width of each table to fit with spacing

        // Generate the first table for the user
        generateTable(doc, margin, 0.5, user, tableWidth);

        // Generate the second table for the user
        generateTable(
            doc,
            margin + tableWidth + spacing,
            0.5,
            user,
            tableWidth
        );

        // Save the PDF with the user's name
        doc.save(`${user.name}.pdf`);
    }
};
</script>
