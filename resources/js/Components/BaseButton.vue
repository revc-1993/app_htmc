<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { getButtonColor } from "@/colors.js";
import BaseIcon from "@/components/BaseIcon.vue";

const props = defineProps({
    label: {
        type: [String, Number],
        default: null,
    },
    icon: {
        type: String,
        default: null,
    },
    iconSize: {
        type: [String, Number],
        default: null,
    },
    href: {
        type: String,
        default: null,
    },
    target: {
        type: String,
        default: null,
    },
    routeName: {
        type: String,
        default: null,
    },
    id: {
        type: Number,
        default: null,
    },
    type: {
        type: String,
        default: null,
    },
    color: {
        type: String,
        default: "info",
    },
    as: {
        type: String,
        default: null,
    },
    small: Boolean,
    outline: Boolean,
    active: Boolean,
    disabled: Boolean,
    roundedFull: Boolean,
    tooltip: {
        type: String,
        default: "",
    },
    location: {
        type: String,
        default: "",
    },
});

const is = computed(() => {
    if (props.as) {
        return props.as;
    }

    if (props.routeName) {
        return Link;
    }

    if (props.href) {
        return "a";
    }

    return "button";
});

const computedType = computed(() => {
    if (is.value === "button") {
        return props.type ?? "button";
    }

    return null;
});

const labelClass = computed(() =>
    props.small && props.icon ? "px-1" : "px-2"
);

const componentClass = computed(() => {
    const base = [
        props.location === "end"
            ? "rounded-tr-lg rounded-br-lg"
            : props.location === "center"
            ? ""
            : props.roundedFull
            ? "rounded-full"
            : "rounded",
        "inline-flex",
        "justify-center",
        "items-center",
        "whitespace-nowrap",
        "focus:outline-none",
        "transition-colors",
        "focus:ring-0",
        "duration-150",
        "border",
        props.disabled ? "cursor-not-allowed" : "cursor-pointer",
        getButtonColor(
            props.color,
            props.outline,
            !props.disabled,
            props.active
        ),
    ];

    if (!props.label && props.icon) {
        base.push("p-1");
    } else if (props.small) {
        base.push("text-sm", props.roundedFull ? "px-3 py-1" : "p-1");
    } else {
        base.push("py-2", props.roundedFull ? "px-6" : "px-3");
    }

    if (props.disabled) {
        base.push(props.outline ? "opacity-50" : "opacity-70");
    }

    return base;
});
</script>

<template>
    <component
        :is="is"
        :class="componentClass"
        :href="
            routeName ? (id ? route(routeName, id) : route(routeName)) : href
        "
        :type="computedType"
        :target="target"
        :disabled="disabled"
        v-tooltip="tooltip"
    >
        <BaseIcon v-if="icon" :path="icon" :size="iconSize" />
        <span v-if="label" :class="labelClass">{{ label }}</span>
    </component>
</template>
