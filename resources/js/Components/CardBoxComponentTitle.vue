<script setup>
import BaseIcon from "@/components/BaseIcon.vue";

defineProps({
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="flex items-center justify-between mb-3 font-bold bg-success">
        <h1 class="text-2xl">
            {{ title }}
        </h1>
        <slot />
    </div>
</template>
