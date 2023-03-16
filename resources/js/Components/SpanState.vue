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
        2: "orange",
        3: "warning",
        4: "green",
        5: "violet",
        6: "success",
        7: "teal",
    }[props.state.id];
});

const pillIcon = computed(() => {
    return {
        danger: mdiTrendingDown,
        orange: mdiTrendingDown,
        warning: mdiTrendingNeutral,
        green: mdiTrendingUp,
        violet: mdiTrendingDown,
        success: mdiTrendingUp,
        teal: mdiTrendingUp,
        info: mdiTrendingNeutral,
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
