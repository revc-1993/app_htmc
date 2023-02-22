<script setup>
import { mdiCardBulleted, mdiCheckBold } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import TableSampleCertifications from "@/components/certifications/TableSampleCertifications.vue";
import { useToast } from "vue-toastification";

// --------------------------------------------
const props = defineProps({
    certifications: Object,
    departments: Object,
});

const getMessage = (operation, message, state) => {
    const colors = {
        1: "success-class",
        3: "warning-class",
        4: "danger-class",
    };
    const messages = `${message}. \nEstado: ${state}`;

    const toast = useToast();
    toast(messages, {
        position: "top-center",
        timeout: 3500,
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
            <div class="mt-4">
                {{ $page.props }}
            </div>
            <SectionTitleLineWithButton
                :icon="mdiCardBulleted"
                title="Certificaciones"
                main
            >
            </SectionTitleLineWithButton>

            <TableSampleCertifications
                :certifications="certifications"
                :departments="departments"
                @alert="
                    getMessage(
                        $event,
                        $page.props.flash.message,
                        $page.props.flash.state
                    )
                "
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>

<style>
.Vue-Toastification__toast--default.height-class {
    height: max-content;
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
