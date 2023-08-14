<script setup>
import { computed, useSlots } from "vue";
import CardBoxComponentBody from "@/Components/CardBoxComponentBody.vue";
import CardBoxComponentFooter from "@/Components/CardBoxComponentFooter.vue";
import { useStyleStore } from "@/stores/style.js";

const styleStore = useStyleStore();

const props = defineProps({
    rounded: {
        type: String,
        default: "rounded-2xl",
    },
    flex: {
        type: String,
        default: "flex-col",
    },
    hasComponentLayout: Boolean,
    hasTable: Boolean,
    isForm: Boolean,
    isHoverable: Boolean,
    isModal: Boolean,
    inModal: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["submit"]);

const slots = useSlots();

const hasFooterSlot = computed(() => slots.footer && !!slots.footer());

const componentClass = computed(() => {
    const base = [
        props.rounded,
        props.flex,
        props.isModal ? "dark:bg-slate-900" : "dark:bg-slate-900/70",
    ];

    if (props.isHoverable) {
        base.push("hover:shadow-lg transition-shadow duration-500");
    }

    if (props.isModal || props.isForm) {
        base.push("overflow-y-auto");
        base.push(
            styleStore.darkMode
                ? "aside-scrollbars-[slate]"
                : "aside-scrollbars-[light]"
        );
    }
    return base;
});

const submit = (event) => {
    emit("submit", event);
};
</script>

<template>
    <component
        :is="isForm ? 'form' : 'div'"
        :class="componentClass"
        class="bg-white flex"
        @submit.prevent="submit"
        scroll-region
    >
        <slot v-if="hasComponentLayout" />
        <template v-else>
            <CardBoxComponentBody :no-padding="hasTable || inModal">
                <slot />
            </CardBoxComponentBody>
            <CardBoxComponentFooter v-if="hasFooterSlot">
                <slot name="footer" />
            </CardBoxComponentFooter>
        </template>
    </component>
</template>
