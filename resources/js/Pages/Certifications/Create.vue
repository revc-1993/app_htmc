<script setup>
import { mdiCardBulleted } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import FormCertification from "@/components/certifications/FormCertification.vue";
import BaseButton from "@/components/BaseButton.vue";

// --------------------------------------------
const props = defineProps({
    departments: Object,
    processTypes: Object,
    expenseTypes: Object,
    budgetLines: Object,
    users: Object,
    recordStatuses: Object,
});

const elementProps = {
    label: "Crear Certificación",
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="elementProps.label" />
        <SectionMain>
            <!-- <p>{{ $page.props.auth.user.roles }}</p> -->
            <SectionTitleLineWithButton
                :icon="mdiCardBulleted"
                :title="elementProps.label"
                main
            >
                <BaseButton
                    route-name="certifications.index"
                    color="lightDark"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </SectionTitleLineWithButton>

            <FormCertification
                :departments="departments"
                :process-types="processTypes"
                :expense-types="expenseTypes"
                :budget-lines="budgetLines"
                :users="users"
                :record-statuses="recordStatuses"
                :element-props="elementProps"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
