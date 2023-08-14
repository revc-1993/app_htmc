<script setup>
import { mdiCardBulleted, mdiCheckBold } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import TableSampleCertifications from "@/Components/Certifications/TableSampleCertifications.vue";
import Toast from "@/Components/Toast.vue";

// --------------------------------------------
const props = defineProps({
    certifications: Object,
    departments: Object,
    processTypes: Object,
    expenseTypes: Object,
    budgetLines: Object,
    users: Object,
    recordStatuses: Object,
    roles: Object,
});
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Certificaciones" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiCardBulleted"
                title="Certificaciones"
                main
            />
            <Toast
                v-if="$page.props.flash.message"
                :message="$page.props.flash.message"
            />

            <TableSampleCertifications
                :certifications="certifications"
                :departments="departments"
                :process-types="processTypes"
                :expense-types="expenseTypes"
                :budget-lines="budgetLines"
                :users="users"
                :record-statuses="recordStatuses"
                :roles="roles"
                instance="certificación"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
