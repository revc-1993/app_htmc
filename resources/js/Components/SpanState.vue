<script setup>
import { computed } from "vue";
import PillTag from "@/components/PillTag.vue";
import { mdiTrendingDown, mdiTrendingUp, mdiTrendingNeutral } from "@mdi/js";
import { useStyleStore } from "@/stores/style.js";

const props = defineProps({
    state: {
        type: Object,
    },
});

const styleStore = useStyleStore();

const colorState = computed(() => {
    return {
        1: "danger",
        2: "warning",
        3: "green",
        4: "violet",
        5: "success",
        6: "teal",
    }[props.state.id];
});

const pillIcon = computed(() => {
    return {
        success: mdiTrendingUp,
        warning: mdiTrendingNeutral,
        danger: mdiTrendingDown,
        teal: mdiTrendingUp,
        violet: mdiTrendingDown,
        info: null,
    }[colorState.value];
});
</script>

<template>
    <PillTag
        :color="colorState"
        :label="state.status"
        :small="true"
        :outline="styleStore.darkMode"
        :icon="pillIcon"
    />
</template>
