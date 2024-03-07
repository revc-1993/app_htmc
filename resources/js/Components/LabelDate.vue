<script setup>
import BaseButtons from "@/Components/BaseButtons.vue";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime"; // Importa el complemento de fechas relativas
import "dayjs/locale/es"; // Si deseas utilizar el idioma español

dayjs.locale("es");
dayjs.extend(relativeTime);

const props = defineProps({
    dateOne: {
        type: [Date, String],
        default: dayjs(),
    },
    dateTwo: {
        type: [Date, String],
        default: dayjs(),
    },
    dateThree: {
        type: [Date, String],
        default: dayjs(),
    },
    dateFour: {
        type: [Date, String],
        default: dayjs(),
    },
    activePhase: Number,
});

// --------------------------------------------
// MANEJO DE FECHAS
// --------------------------------------------
const timeAgo = (date) => {
    return dayjs(date).isValid() ? dayjs(date).fromNow() : "-";
};

const daysDifference = (startDate, endDate) => {
    const start = dayjs(startDate);
    const end = dayjs(endDate);

    if (start.isValid() && end.isValid()) {
        const differenceInDays = end.from(start, true); // Calcula la diferencia en días
        return `${differenceInDays}`;
    } else {
        return "-";
    }
};
</script>

<template>
    <BaseButtons type="justify-end">
        <template v-if="activePhase === 1">
            <div class="gap-x-3 text-right">
                <strong>Fecha de ingreso: </strong>
                {{ dateOne }}
                <p
                    class="text-gray-500 dark:text-slate-400 text-sm lowercase first-letter:capitalize"
                >
                    <b>{{ timeAgo(dateOne) }}</b>
                </p>
            </div>
        </template>
        <template v-if="activePhase === 2">
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de asignación JAPC: </strong>
                {{ dateTwo }}
                <p class="text-gray-500 dark:text-slate-400">
                    <b>{{ daysDifference(dateOne, dateTwo) }}</b>
                </p>
            </div>
        </template>
        <template v-if="activePhase === 3">
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de registro: </strong>
                {{ dateThree }}
                <p class="text-gray-500 dark:text-slate-400">
                    <b>{{ daysDifference(dateTwo, dateThree) }}</b>
                </p>
            </div>
        </template>
        <template v-if="activePhase === 4">
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de revisión: </strong>
                {{ dateFour }}
                <p class="text-gray-500 dark:text-slate-400">
                    <b>{{ daysDifference(dateThree, dateFour) }}</b>
                </p>
            </div>
        </template>
    </BaseButtons>
</template>
