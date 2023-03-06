<script setup>
import { computed } from "vue";
import PillTag from "@/components/PillTag.vue";
import { mdiTrendingDown, mdiTrendingUp, mdiTrendingNeutral } from "@mdi/js";

const props = defineProps({
    state: {
        type: Object,
    },
});

const colorState = computed(() => {
    return {
        1: "danger",
        2: "warning",
        3: "success",
        4: "violet",
        5: "teal",
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
        :outline="false"
        :icon="pillIcon"
    />
</template>
