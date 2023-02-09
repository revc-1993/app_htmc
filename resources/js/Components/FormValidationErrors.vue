<script setup>
import NotificationBar from "@/components/NotificationBar.vue";
</script>

<template>
    <NotificationBar color="danger">
        <b>¡Vaya! Su formulario contiene errores.</b>
        <!-- <span v-for="(error, key) in errors" :key="key">{{ error }}</span> -->
    </NotificationBar>
</template>
