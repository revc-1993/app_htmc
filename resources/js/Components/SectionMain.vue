<script setup>
import { containerMaxW } from "@/config.js";
</script>

<template>
    <section class="p-6 text-sm" :class="containerMaxW">
        <slot />
    </section>
</template>
