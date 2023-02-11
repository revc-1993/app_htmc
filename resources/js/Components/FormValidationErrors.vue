<script setup>
import NotificationBar from "@/components/NotificationBar.vue";
import { useToast } from "vue-toastification";
import { createToaster } from "@meforma/vue-toaster";

const toast = useToast();
toast("¡Vaya! Su formulario contiene errores.", {
    timeout: 2500,
    toastClassName: "my-custom-toast-class",
    bodyClassName: "custom-class",
    hideProgressBar: true,
});

// const toast = createToaster();
// toast.show("¡Vaya! Su formulario contiene errores.", {
//     position: "top-right",
//     type: "error",
// });
</script>

<template></template>

<style>
.Vue-Toastification__toast--default.my-custom-toast-class {
    background-color: rgb(220 38 38);
    height: 0.75rem; /* 12px */
}
/* Applied to the toast body when using regular strings as content */
.Vue-Toastification__toast-body.custom-class {
    font-size: 0.875rem; /* 14px */
    line-height: 1.25rem; /* 20px */
}
</style>
