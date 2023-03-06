<script setup>
import { computed } from "vue";
import { mdiCardBulleted, mdiCheckBold } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import TableSampleCertifications from "@/components/certifications/TableSampleCertifications.vue";
import { useToast } from "vue-toastification";
import StepperComponent from "@/components/StepperComponent.vue";
import Stepper from "@/components/Stepper.vue";

// --------------------------------------------
const props = defineProps({
    certifications: Object,
    departments: Object,
    process_types: Object,
    expense_types: Object,
    budget_lines: Object,
    users: Object,
    record_statuses: Object,
});

const getMessage = (operation, message) => {
    const colors = {
        1: "success-class",
        3: "warning-class",
        4: "danger-class",
    };
    const messages = computed(() => {
        return `${message}`;
    });

    const toast = useToast();
    toast(messages.value, {
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
            <!-- <p>{{ $page.props.auth.user.roles }}</p> -->
            <SectionTitleLineWithButton
                :icon="mdiCardBulleted"
                title="Certificaciones"
                main
            >
            </SectionTitleLineWithButton>

            <TableSampleCertifications
                :certifications="certifications"
                :departments="departments"
                :process_types="process_types"
                :expense_types="expense_types"
                :budget_lines="budget_lines"
                :users="users"
                :record_statuses="record_statuses"
                @alert="getMessage($event, $page.props.flash.message)"
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
