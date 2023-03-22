<script setup>
import { mdiCardBulleted, mdiCheckBold } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import TableSampleCertifications from "@/components/certifications/TableSampleCertifications.vue";
import Toast from "@/components/Toast.vue";

// --------------------------------------------
const props = defineProps({
    certifications: Object,
    departments: Object,
    processTypes: Object,
    expenseTypes: Object,
    budgetLines: Object,
    users: Object,
    recordStatuses: Object,
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
                :recordStatuses="recordStatuses"
                instance="certificación"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
