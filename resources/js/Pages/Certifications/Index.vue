<script setup>
import { mdiCardBulleted, mdiCheckBold } from "@mdi/js";
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import TableSampleCertifications from "@/components/certifications/TableSampleCertifications.vue";
import NotificationBar from "@/components/NotificationBar.vue";
// --------------------------------------------

const props = defineProps({
    certifications: Object,
});

// --------------------------------------------
// MOSTRAR MENSAJES DE ALERTA
// --------------------------------------------
const showAlert = ref(false);
const message = ref("");
const color = ref("");

const getMessage = (operation) => {
    // showAlert.value = false;
    let messages = `Registro ${
        operation === "1"
            ? "ingresado "
            : operation === "3"
            ? "actualizado "
            : operation === "4"
            ? "eliminado "
            : "leído "
    } satisfactoriamente`;
    message.value = messages;
    color.value =
        operation === "1"
            ? "success"
            : operation === "3"
            ? "info"
            : operation === "4"
            ? "danger"
            : "info";
    // showAlert.value = true;
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Certificaciones" />
        <SectionMain>
            <NotificationBar
                v-if="showAlert"
                :icon="mdiCheckBold"
                :color="color"
            >
                <b>Transacción completa.</b> {{ message }}
            </NotificationBar>

            <SectionTitleLineWithButton
                :icon="mdiCardBulleted"
                title="Certificaciones"
                main
            >
            </SectionTitleLineWithButton>

            <TableSampleCertifications
                v-model="showAlert"
                :certifications="certifications"
                @alert="getMessage"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
