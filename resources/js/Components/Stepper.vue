<script setup>
import BaseIcon from "@/components/BaseIcon.vue";
import { computed, ref } from "vue";

const props = defineProps({
    operation: String,
    state: {
        type: String,
        default: "",
    },
});

const borderColor = computed(() => {
    return {
        "Pendiente de revisión": "border-red-500",
        "En revisión": "border-yellow-500",
        Devuelto: "border-violet-500",
        Certificado: "border-emerald-500",
        Observado: "border-emerald-500",
    }[props.state];
});
const textColor = computed(() => {
    return {
        "Pendiente de revisión": "text-red-500",
        "En revisión": "text-yellow-500",
        Devuelto: "text-violet-500",
        Certificado: "text-emerald-500",
        Observado: "text-emerald-500",
    }[props.state];
});
const borderInactive = "border-gray-500";
const textInactive = "text-gray-500";
</script>

<template>
    <div class="grid grid-cols-5 gap-4 w-3/4 m-auto mt-7">
        <div
            class="border-t-4"
            :class="
                state == 'Pendiente de revisión' ? borderColor : borderInactive
            "
        >
            <p
                class="font-semibold"
                :class="
                    state == 'Pendiente de revisión' ? textColor : textInactive
                "
            >
                Pendiente de revisión
            </p>
        </div>
        <div
            class="border-t-4"
            :class="state == 'En revisión' ? borderColor : borderInactive"
        >
            <p
                class="font-semibold"
                :class="state == 'En revisión' ? textColor : textInactive"
            >
                En revisión
            </p>
        </div>
        <div
            class="border-t-4"
            :class="state == 'Devuelto' ? borderColor : borderInactive"
        >
            <p
                class="font-semibold"
                :class="state == 'Devuelto' ? textColor : textInactive"
            >
                Devuelto
            </p>
        </div>
        <div
            class="border-t-4"
            :class="state == 'Certificado' ? borderColor : borderInactive"
        >
            <p
                class="font-semibold"
                :class="state == 'Certificado' ? textColor : textInactive"
            >
                Certificado
            </p>

            <!-- <p class="font-semibold"></p> -->
        </div>
        <div
            class="border-t-4"
            :class="state == 'Observado' ? borderColor : borderInactive"
        >
            <p
                class="font-semibold"
                :class="state == 'Observado' ? textColor : textInactive"
            >
                Observado
            </p>
        </div>
    </div>
</template>
