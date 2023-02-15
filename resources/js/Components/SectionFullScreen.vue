<script setup>
import { computed } from "vue";
import { useStyleStore } from "@/stores/style";
import {
    gradientBgPurplePink,
    gradientBgDark,
    gradientBgPinkRed,
    gradientBgSlateGray,
} from "@/colors";

const props = defineProps({
    bg: {
        type: String,
        required: true,
        validator: (value) => ["slateGray", "pinkRed"].includes(value),
    },
});

const colorClass = computed(() => {
    if (useStyleStore().darkMode) {
        return gradientBgDark;
    }

    switch (props.bg) {
        case "purplePink":
            return gradientBgPurplePink;
        case "pinkRed":
            return gradientBgPinkRed;
        case "slateGray":
            return gradientBgSlateGray;
    }

    return "";
});

// style="
//     background-image: url('https://www.htmc.gob.ec/wp-content/uploads/2021/07/20210719-QUIROFANO-NO-1280x663.jpeg');
// "
</script>

<template>
    <div
        class="flex min-h-screen h-full w-full items-center justify-center bg-cover bg-left-bottom bg-fixed"
        :class="colorClass"
        style="
            background-image: url('https://www.htmc.gob.ec/wp-content/uploads/2021/07/20210719-QUIROFANO-NO-1280x663.jpeg');
        "
    >
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>
        <slot
            card-class="absolute w-11/12 md:w-7/12 lg:w-6/12 xl:w-4/12 shadow-2xl"
        />
    </div>
</template>
