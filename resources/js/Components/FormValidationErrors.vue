<script setup>
import NotificationBar from "@/components/NotificationBar.vue";
import { useToast } from "vue-toastification";

const toast = useToast();
toast.error("¡Vaya! Su formulario contiene errores.", {
    timeout: 5000,
});
</script>

<template></template>
