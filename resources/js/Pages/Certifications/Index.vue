<script setup>
import { mdiCardBulleted, mdiCheckBold } from "@mdi/js";
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import TableSampleCertifications from "@/components/certifications/TableSampleCertifications.vue";
import { TYPE, useToast } from "vue-toastification";
import { createToaster } from "@meforma/vue-toaster";
import { parse } from "@vue/compiler-dom";

// --------------------------------------------
const props = defineProps({
    certifications: Object,
});

// --------------------------------------------
// MOSTRAR MENSAJES DE ALERTA
// --------------------------------------------
const getMessage = (operation) => {
    const operations = parseInt(operation);
    const messages = {
        1: "ingresado",
        3: "actualizado",
        4: "eliminado",
    };
    const colors = {
        1: "success-class",
        3: "warning-class",
        4: "danger-class",
    };

    const toast = useToast();
    toast("Registro " + (messages[operation] || "leído") + " correctamente.", {
        timeout: 2500,
        // type: colors[operation] || TYPE.INFO,
        toastClassName: ["height-class", colors[operation] || "blue-class"],
        bodyClassName: "custom-class",
        hideProgressBar: true,
    });
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Certificaciones" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiCardBulleted"
                title="Certificaciones"
                main
            >
            </SectionTitleLineWithButton>

            <TableSampleCertifications
                :certifications="certifications"
                @alert="getMessage"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>

<style>
.Vue-Toastification__toast--default.height-class {
    height: 0.75rem; /* 12px */
}
.Vue-Toastification__toast--default.danger-class {
    background-color: rgb(220 38 38);
}
.Vue-Toastification__toast--default.warning-class {
    background-color: rgb(202 138 4);
}
.Vue-Toastification__toast--default.success-class {
    background-color: rgb(5 150 105);
}
.Vue-Toastification__toast--default.blue-class {
    background-color: rgb(37 99 235);
}

.Vue-Toastification__toast-body.custom-class {
    font-size: 0.875rem; /* 14px */
    line-height: 1.25rem; /* 20px */
}
</style>
