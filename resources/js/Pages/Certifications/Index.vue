<script setup>
import { mdiCardBulleted, mdiCheckBold } from "@mdi/js";
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import TableSampleCertifications from "@/components/certifications/TableSampleCertifications.vue";
import { TYPE, useToast } from "vue-toastification";
// --------------------------------------------
const props = defineProps({
    certifications: Object,
});

// --------------------------------------------
// MOSTRAR MENSAJES DE ALERTA
// --------------------------------------------
const getMessage = (operation) => {
    // showAlert.value = false;
    const messages = {
        1: "ingresado",
        3: "actualizado",
        4: "eliminado",
    };
    const message =
        "Registro " + (messages[operation] || "leído") + " correctamente.";

    const colors = {
        1: TYPE.SUCCESS,
        3: TYPE.WARNING,
        4: TYPE.ERROR,
    };
    const toast = useToast();

    toast(message, {
        timeout: 5000,
        type: colors[operation] || TYPE.INFO,
    });
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Certificaciones" />
        <SectionMain>
            <!-- <NotificationBar
                v-if="showAlert"
                :icon="mdiCheckBold"
                :color="color"
            >
                <b>Transacción completa.</b> {{ message }}
            </NotificationBar> -->

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
