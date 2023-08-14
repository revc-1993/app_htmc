<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    operation: {
        type: [String, Number],
        default: null,
    },
    currentManagement: Number,
    modelValue: {
        type: [String, Number, Boolean, Object],
        default: null,
    },
    steps: Array,
});

const index = ref(1);

const olClass = computed(() => {
    const base = [
        "grid",
        "grid-cols-1",
        "divide-x",
        "divide-gray-200",
        "overflow-hidden",
        "rounded-lg",
        "border",
        "border-stone-400",
        "text-sm",
        "text-gray-500",
    ];
    base.push(
        props.steps?.length === 4
            ? "sm:grid-cols-4"
            : props.steps?.length === 3
            ? "sm:grid-cols-3"
            : "sm:grid-cols-2"
    );

    return base;
});

const liClass = (step) => {
    const base = [
        step > 1 ? "relative" : "",
        "flex",
        "items-center",
        "justify-center",
        "gap-2",
        "p-3",
        "cursor-pointer",
    ];
    if (props.operation !== 1 && props.currentManagement === step) {
        base.push(
            "bg-emerald-600",
            "hover:bg-emerald-700",
            "text-white",
            "dark:outline-emerald-600",
            "dark:hover:outline-emerald-700"
        );
    } else if (step === value.value) {
        base.push("bg-slate-200");
    } else {
        base.push(
            // "group",
            // "peer",
            "bg-white",
            "dark:bg-slate-600",
            "dark:text-white",
            "hover:bg-slate-100",
            "active:bg-slate-300",
            "active:text-white",
            "focus:bg-slate-200"
        );
    }
    return base;
};

const spanClass = (step, position) => {
    const base = ["absolute"];
    base.push(position === "left" ? "-left-2" : "-right-2 ");
    base.push(
        "top-1/2",
        "hidden",
        "h-4",
        "w-4",
        "-translate-y-1/2",
        "rotate-45",
        "border",
        "border-b-0",
        "border-l-0",
        "sm:block",
        "cursor-pointer"
    );
    if (props.currentManagement === step) {
        base.push("bg-emerald-600");
    } else {
        base.push(
            "bg-gray-50"
            // "hover:bg-gray-200",
            // position === "right" ? "group-hover:bg-gray-200" : "",
            // position === "left" ? "peer-hover:bg-gray-200" : ""
            // "focus:bg-stone-200",
            // "focus:outline-none",
            // "focus:ring-2",
            // "focus:ring-stone-400"
        );
    }
    return base;
};

const tabindex = (step) => {
    return step === value.value ? "0" : "1";
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
        <div class="mb-6">
            <ol :class="olClass">
                <li
                    v-for="step in steps"
                    :key="step.id"
                    :class="liClass(step.id)"
                    :tabindex="tabindex(step.id)"
                    @click.prevent="stepping(step.id)"
                >
                    <svg
                        v-if="step.id === 1"
                        class="h-7 w-7 flex-shrink-0"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"
                        />
                    </svg>
                    <svg
                        v-else-if="step.id === 2"
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

                    <svg
                        v-else-if="step.id >= 3"
                        class="h-7 w-7 flex-shrink-0"
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
                        <strong class="block font-medium">
                            {{ step.id }}.
                        </strong>
                        <small class="mt-1"> {{ step.nickname }} </small>
                    </p>
                </li>

                <!-- <li
                    :class="liClass(1)"
                    @click.prevent="stepping(1)"
                    :tabindex="tabindex(1)"
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
                        <small class="mt-1"> Secretaría CGF </small>
                    </p>
                </li>

                <li
                    class="relative"
                    :class="liClass(2)"
                    @click.prevent="stepping(2)"
                    :tabindex="tabindex(2)"
                >
                    <span :class="spanClass(1, 'left')"> </span>
                    <span :class="spanClass(2, 'right')"> </span>
                    <svg
                        class="h-7 w-7 flex-shrink-0"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"
                        />
                    </svg>

                    <p class="leading-none">
                        <strong class="block font-medium"> 2. </strong>
                        <small class="mt-1"> Secretaría JAPC - CP </small>
                    </p>
                </li>

                <li
                    class="relative"
                    :class="liClass(3)"
                    @click.prevent="stepping(3)"
                    tabindex="1"
                >
                    <span :class="spanClass(2, 'left')" />
                    <span :class="spanClass(3, 'right')" />

                    <svg
                        class="h-7 w-7 flex-shrink-0"
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
                        <strong class="block font-medium"> 3. </strong>
                        <small class="mt-1"> Analista de Certificación </small>
                    </p>
                </li>
                <li
                    :class="liClass(4)"
                    @click.prevent="stepping(4)"
                    tabindex="1"
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
                        <strong class="block font-medium"> 4. </strong>
                        <small class="mt-1">
                            Coordinación General Financiera.
                        </small>
                    </p>
                </li> -->
            </ol>
        </div>
    </div>
</template>

<style>
.set-focus:focus {
    background-color: rgb(229 231 235);
}
</style>
