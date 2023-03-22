<script setup>
import { mdiTextBoxMultipleOutline, mdiBackspace } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import FormCommitment from "@/components/commitments/FormCommitment.vue";
import BaseButton from "@/components/BaseButton.vue";

// --------------------------------------------
const props = defineProps({
    users: Object,
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
                :record-statuses="recordStatuses"
                :current-operation="1"
                :element-props="elementProps"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
