<script setup>
import { computed } from "vue";
import BaseIcon from "@/components/BaseIcon.vue";
import { mdiCalendarCheck, mdiCalendarClock, mdiCalendarAlert } from "@mdi/js";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime"; // Importa el complemento de fechas relativas
import "dayjs/locale/es"; // Si deseas utilizar el idioma español

dayjs.locale("es");
dayjs.extend(relativeTime);

const props = defineProps({
    certification: {
        type: Object,
        default: null,
    },
});

const startDate = computed(() => {
    const date = {
        1: props.certification.sec_cgf_date,
        2: props.certification.assignment_date,
        3: props.certification.cp_date,
        4: props.certification.coord_cgf_date,
    }[props.certification.current_management - 1];
    return dayjs(date).format("YYYY-MM-DD");
});

const timeAgo = computed(() => {
    return dayjs(startDate.value).isValid()
        ? dayjs().from(startDate, true)
        : "";
});

const manageDate = computed(() => {
    return dayjs(startDate.value).isValid()
        ? dayjs().diff(dayjs(startDate.value), "day")
        : 0;
});

const dateIcon = computed(() => {
    return manageDate.value <= 3
        ? mdiCalendarCheck
        : manageDate.value > 3 && manageDate.value <= 7
        ? mdiCalendarClock
        : manageDate.value > 7
        ? mdiCalendarAlert
        : "";
});

const dateClass = computed(() => {
    const icon = dateIcon.value;
    const classMapping = {
        [mdiCalendarCheck]: "text-green-500",
        [mdiCalendarClock]: "text-yellow-500",
        [mdiCalendarAlert]: "text-red-500",
    };
    return classMapping[icon] || "";
});
</script>

<template>
    <div :class="dateClass">
        <BaseIcon v-if="dateIcon" :path="dateIcon" class="mr-1 mb-0" />
        <strong>
            {{ startDate }}
        </strong>
    </div>
</template>
