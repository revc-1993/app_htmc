<script setup>
import { computed } from "vue";

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "checkbox",
        validator: (value) => ["checkbox", "radio", "switch"].includes(value),
    },
    label: {
        type: String,
        default: null,
    },
    modelValue: {
        type: [Array, String, Number, Boolean],
        default: null,
    },
    inputValue: {
        type: [String, Number, Boolean],
        required: true,
    },
    disabled: Boolean,
});

const emit = defineEmits(["update:modelValue"]);

const computedValue = computed({
    get: () => props.modelValue,
    set: (value) => {
        emit("update:modelValue", value);
    },
});

const inputType = computed(() =>
    props.type === "radio" ? "radio" : "checkbox"
);

const inputElClass = computed(() => {
    const base = [
        props.disabled
            ? "text-slate-400 dark:text-slate-300 border-slate-400"
            : "border-slate-700 focus:ring-blue-600",
    ];
    return base;
});
</script>

<template>
    <label :class="{ type, inputElClass }">
        <input
            v-model="computedValue"
            :type="inputType"
            :name="name"
            :value="inputValue"
            :disabled="disabled"
            :class="inputElClass"
        />
        <span class="check" />
        <span class="pl-2">{{ label }}</span>
    </label>
</template>
