<script setup>
import { mdiTextBoxMultipleOutline, mdiBackspace } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import FormCommitment from "@/Components/Commitments/FormCommitment.vue";
import BaseButton from "@/Components/BaseButton.vue";

// --------------------------------------------
const props = defineProps({
    users: Object,
    roles: Object,
    recordStatuses: Object,
});

const elementProps = {
    label: "Crear Compromiso",
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="elementProps.label" />
        <SectionMain>
            <!-- <p>{{ $page.props.auth.user.roles }}</p> -->
            <SectionTitleLineWithButton
                :icon="mdiTextBoxMultipleOutline"
                :title="elementProps.label"
                main
            >
                <BaseButton
                    route-name="commitments.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </SectionTitleLineWithButton>

            <FormCommitment
                :users="users"
                :roles="roles"
                :record-statuses="recordStatuses"
                :current-operation="1"
                :element-props="elementProps"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
