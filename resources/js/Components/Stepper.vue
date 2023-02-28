<script setup>
import BaseIcon from "@/components/BaseIcon.vue";
import { computed, ref } from "vue";

const props = defineProps({
    operation: String,
    state: {
        type: String,
        default: "",
    },
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
});

const liClass = (isactive) => {
    const base = [
        "flex",
        "items-center",
        "justify-center",
        "p-3",
        "cursor-pointer",
    ];
    if (isactive) {
        base.push("bg-sky-700", "text-white");
    } else {
        base.push("hover:bg-stone-400", "hover:text-white");
    }
    return base;
};

const emit = defineEmits(["update:modelValue", "stepping"]);
const value = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
const confirmCancel = (step) => {
    value.value = step;
    emit("stepping", step);
};
const stepping = (step) => {
    confirmCancel(parseInt(step));
};
</script>

<template>
    <div>
        <div class="py-2">
            <ol
                class="grid grid-cols-1 divide-x divide-gray-200 overflow-hidden rounded-lg border bg-stone-100 border-stone-400 text-gray-500 sm:grid-cols-4"
            >
                <li :class="liClass(value === 1)" @click.prevent="stepping(1)">
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
                        <strong class="block font-medium"> 1. </strong>
                        <strong class="mt-1 text-sm"> Secretaría CGF </strong>
                    </p>
                </li>

                <li
                    class="relative"
                    :class="liClass(value === 2)"
                    @click.prevent="stepping(2)"
                >
                    <span
                        class="absolute -left-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                        :class="{ 'bg-sky-700 text-white': value === 2 }"
                    >
                    </span>

                    <span
                        class="absolute -right-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                        :class="{ 'bg-sky-700 text-white': value === 1 }"
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
                        <strong class="mt-1"> Secretaría JAPC - CP </strong>
                    </p>
                </li>

                <li
                    class="relative"
                    :class="liClass(value === 3)"
                    @click.prevent="stepping(3)"
                >
                    <span
                        class="absolute -left-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
                    >
                    </span>

                    <span
                        class="absolute -right-2 top-1/2 hidden h-4 w-4 -translate-y-1/2 rotate-45 border border-b-0 border-l-0 border-gray-300 sm:block"
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
                        <small class="mt-1"> Analista de Certificación </small>
                    </p>
                </li>
                <li :class="liClass(value === 4)" @click.prevent="stepping(4)">
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
                        <strong class="block font-medium"> 4. </strong>
                        <small class="mt-1"> Tesorería. </small>
                    </p>
                </li>
            </ol>
        </div>
    </div>
</template>
