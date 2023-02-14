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

const colorActive = computed(() => {
    return {
        "Pendiente de revisión": "bg-red-500 text-gray-100",
        "En revisión": "bg-yellow-500 text-gray-100",
        Devuelto: "bg-violet-500 text-gray-100",
        Certificado: "bg-emerald-500 text-gray-100",
        Observado: "bg-emerald-500 text-gray-100",
    }[props.state];
});
const colorInactive = "bg-gray-100";
</script>

<template>
    <div>
        <h2 class="sr-only">Steps</h2>

        <div>
            <ol
                class="grid grid-cols-1 divide-x divide-gray-300 overflow-hidden rounded-lg border border-gray-100 text-sm text-gray-500 sm:grid-cols-5"
            >
                <li
                    class="flex items-center justify-center p-4"
                    :class="
                        state == 'Pendiente de revisión'
                            ? colorActive
                            : colorInactive
                    "
                >
                    <p class="leading-none">
                        <strong class="block font-medium"> 1. </strong>
                        <small class="mt-1"> Pendiente de revisión </small>
                    </p>
                </li>

                <li
                    class="relative flex items-center justify-center p-4"
                    :class="
                        state == 'En revisión' ? colorActive : colorInactive
                    "
                >
                    <span
                        class="absolute -left-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                        :class="
                            state == 'Pendiente de revisión'
                                ? colorActive
                                : colorInactive
                        "
                    >
                    </span>

                    <span
                        class="absolute -right-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                        :class="
                            state == 'En revisión' ? colorActive : colorInactive
                        "
                    >
                    </span>

                    <svg
                        class="mr-2 h-7 w-7 flex-shrink-0"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>

                    <p class="leading-none">
                        <strong class="block font-medium"> 2. </strong>
                        <small class="mt-1"> En revisión </small>
                    </p>
                </li>

                <li
                    class="relative flex items-center justify-center p-4"
                    :class="state == 'Devuelto' ? colorActive : colorInactive"
                >
                    <span
                        class="absolute -left-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                        :class="
                            state == 'En revisión' ? colorActive : colorInactive
                        "
                    >
                    </span>

                    <span
                        class="absolute -right-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                        :class="
                            state == 'Devuelto' ? colorActive : colorInactive
                        "
                    >
                    </span>

                    <svg
                        class="mr-2 h-7 w-7 flex-shrink-0"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>

                    <p class="leading-none">
                        <strong class="block font-medium"> 3. </strong>
                        <small class="mt-1"> Devuelto </small>
                    </p>
                </li>

                <li
                    class="relative flex items-center justify-center p-4"
                    :class="
                        state == 'Certificado' ? colorActive : colorInactive
                    "
                >
                    <span
                        class="absolute -left-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                        :class="
                            state == 'Devuelto' ? colorActive : colorInactive
                        "
                    >
                    </span>

                    <span
                        class="absolute -right-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                        :class="
                            state == 'Certificado' ? colorActive : colorInactive
                        "
                    >
                    </span>

                    <p class="leading-none">
                        <strong class="block font-medium"> 4. </strong>
                        <small class="mt-1"> Certificado </small>
                    </p>
                </li>

                <li
                    class="flex items-center justify-center p-4"
                    :class="state == 'Observado' ? colorActive : colorInactive"
                >
                    <svg
                        class="mr-2 h-7 w-7 flex-shrink-0"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                        />
                    </svg>

                    <p class="leading-none">
                        <strong class="block font-medium"> 5. </strong>
                        <small class="mt-1"> Observado. </small>
                    </p>
                </li>
            </ol>
        </div>
    </div>
</template>
