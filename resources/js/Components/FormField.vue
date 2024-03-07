<script setup>
import { computed, useSlots } from "vue";

defineProps({
    label: {
        type: String,
        default: null,
    },
    labelFor: {
        type: String,
        default: null,
    },
    help: {
        type: String,
        default: null,
    },
    errors: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const slots = useSlots();

const wrapperClass = computed(() => {
    const base = [];
    const slotsLength = slots.default().length;

    if (slotsLength > 1) {
        base.push("grid grid-cols-1 gap-x-3");
    }

    if (slotsLength === 2) {
        base.push("lg:grid-cols-2");
    }

    if (slotsLength === 3) {
        base.push("lg:grid-cols-3");
    }

    return base;
});
</script>

<template>
    <div class="mb-6 last:mb-0">
        <label v-if="label" :for="labelFor" class="block font-bold mb-2"
            >{{ label }}
            <b v-show="required" class="text-red-500">*</b>
        </label>
        <div :class="wrapperClass">
            <slot />
        </div>
        <div v-if="errors" class="text-sm text-red-500 dark:text-red-400 mt-1">
            * {{ errors }}
        </div>
        <div
            v-else-if="help"
            class="text-xs text-gray-500 dark:text-slate-400 mt-1"
        >
            {{ help }}
        </div>
    </div>
</template>
