<script setup>
import { computed } from "vue";
import PillTag from "@/components/PillTag.vue";
import { mdiTrendingDown, mdiTrendingUp, mdiTrendingNeutral } from "@mdi/js";

const props = defineProps({
    state: {
        type: String,
    },
});

// const state = props.state;

// const colorState = computed(() => {
//     switch (props.state) {
//         case "Pendiente de revisión":
//             return "bg-red-200 text-red-600 ";
//         case "En revisión":
//             return "bg-yellow-200 text-yellow-600";
//         case "Certificado":
//             return "bg-green-200 text-green-600";
//         case "Observado":
//             return "bg-emerald-200 text-emerald-600";
//         case "Devuelto":
//             return "bg-violet-200 text-violet-600";
//     }
// });

const colorState = computed(() => {
    return {
        "Pendiente de revisión": "danger",
        "En revisión": "warning",
        Certificado: "success",
        Observado: "teal",
        Devuelto: "violet",
    }[props.state];
    // switch (props.state) {
    //     case "Pendiente de revisión":
    //         return "danger";
    //     case "En revisión":
    //         return "warning";
    //     case "Certificado":
    //         return "success";
    //     case "Observado":
    //         return "teal";
    //     case "Devuelto":
    //         return "violet";
    // }
});

const pillIcon = computed(() => {
    return {
        success: mdiTrendingUp,
        warning: mdiTrendingNeutral,
        danger: mdiTrendingDown,
        teal: mdiTrendingUp,
        violet: mdiTrendingDown,
        info: null,
    }[colorState.value];
});
</script>

<template>
    <!-- <span class="py-1 px-3 rounded-full border text-sm" :class="colorState">
        <span class="h-2.5 w-2.5 text-xl">•</span>
        {{ state }}</span
    > -->

    <PillTag
        :color="colorState"
        :label="state"
        :small="true"
        :outline="false"
        :icon="pillIcon"
    />
</template>
