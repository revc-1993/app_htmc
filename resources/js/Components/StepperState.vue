<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    state: {
        type: String,
        default: "",
    },
});

const colorLi = computed(() => {
    return {
        "Pendiente de revisión": "text-red-600 dark:text-red-500",
        "En revisión": "text-yellow-600 dark:text-yellow-500",
        Certificado: "text-green-600 dark:text-green-500",
        Observado: "text-emerald-600 dark:text-emerald-500",
        Devuelto: "text-violet-600 dark:text-violet-500",
    }[props.state];
});

const colorSpan = computed(() => {
    return {
        "Pendiente de revisión": "border-red-600 dark:border-red-500",
        "En revisión": "border-yellow-600 dark:border-yellow-500",
        Certificado: "border-green-600 dark:border-green-500",
        Observado: "border-emerald-600 dark:border-emerald-500",
        Devuelto: "border-violet-600 dark:border-violet-500",
    }[props.state];
});
</script>

<template>
    <ol
        class="flex items-center w-full p-3 mb-6 text-sm font-medium text-center text-gray-500 bg-white dark:text-gray-400 sm:text-base dark:bg-gray-800 sm:p-4 sm:space-x-4"
    >
        <li
            class="flex items-center"
            :class="state == 'Pendiente de revisión' ? colorLi : ''"
        >
            <span
                class="flex items-center justify-center w-5 h-5 mr-2 text-xs border rounded-full shrink-0"
                :class="
                    state == 'Pendiente de revisión'
                        ? colorSpan
                        : 'border-gray-500 dark:border-gray-400'
                "
            >
                1
            </span>
            Pendiente de revisión
            <svg
                aria-hidden="true"
                class="w-4 h-4 ml-2 sm:ml-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 5l7 7-7 7M5 5l7 7-7 7"
                ></path>
            </svg>
        </li>
        <li
            class="flex items-center"
            :class="state == 'En revisión' ? colorLi : ''"
        >
            <span
                class="flex items-center justify-center w-5 h-5 mr-2 text-xs border rounded-full shrink-0"
                :class="
                    state == 'Pendiente de revisión'
                        ? colorSpan
                        : 'border-gray-500 dark:border-gray-400'
                "
            >
                2
            </span>
            En revisión
            <svg
                aria-hidden="true"
                class="w-4 h-4 ml-2 sm:ml-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 5l7 7-7 7M5 5l7 7-7 7"
                ></path>
            </svg>
        </li>
        <li
            class="flex items-center"
            :class="state == 'Certificado' ? colorLi : ''"
        >
            <span
                class="flex items-center justify-center w-5 h-5 mr-2 text-xs border rounded-full shrink-0"
                :class="
                    state == 'Pendiente de revisión'
                        ? colorSpan
                        : 'border-gray-500 dark:border-gray-400'
                "
            >
                3
            </span>
            Certificado
            <svg
                aria-hidden="true"
                class="w-4 h-4 ml-2 sm:ml-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 5l7 7-7 7M5 5l7 7-7 7"
                ></path>
            </svg>
        </li>
        <li
            class="flex items-center"
            :class="state == 'Observado' ? colorLi : ''"
        >
            <span
                class="flex items-center justify-center w-5 h-5 mr-2 text-xs border rounded-full shrink-0"
                :class="
                    state == 'Pendiente de revisión'
                        ? colorSpan
                        : 'border-gray-500 dark:border-gray-400'
                "
            >
                4
            </span>
            Observado
            <svg
                aria-hidden="true"
                class="w-4 h-4 ml-2 sm:ml-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 5l7 7-7 7M5 5l7 7-7 7"
                ></path>
            </svg>
        </li>
        <li
            class="flex items-center"
            :class="state == 'Devuelto' ? colorLi : ''"
        >
            <span
                class="flex items-center justify-center w-5 h-5 mr-2 text-xs border rounded-full shrink-0"
                :class="
                    state == 'Pendiente de revisión'
                        ? colorSpan
                        : 'border-gray-500 dark:border-gray-400'
                "
            >
                5
            </span>
            Devuelto
        </li>
    </ol>
</template>
