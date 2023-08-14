<script setup>
import { mdiCardBulleted, mdiBackspace } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import FormCertification from "@/components/certifications/FormCertification.vue";
import BaseButton from "@/Components/BaseButton.vue";

// --------------------------------------------
const props = defineProps({
    certification: Object,
    departments: Object,
    processTypes: Object,
    expenseTypes: Object,
    budgetLines: Object,
    users: Object,
    roles: Object,
    recordStatuses: Object,
});

const elementProps = {
    label: "Editar Certificación",
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
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </SectionTitleLineWithButton>

            <FormCertification
                :certification="certification"
                :departments="departments"
                :process-types="processTypes"
                :expense-types="expenseTypes"
                :budget-lines="budgetLines"
                :users="users"
                :roles="roles"
                :record-statuses="recordStatuses"
                :current-operation="3"
                :element-props="elementProps"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
