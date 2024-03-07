<script setup>
import { mdiTextBoxMultipleOutline, mdiBackspace } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import FormPayment from "@/Components/Payments/FormPayment.vue";
import BaseButton from "@/Components/BaseButton.vue";

// --------------------------------------------
const props = defineProps({
    payment: Object,
    users: Object,
    roles: Object,
    recordStatuses: Object,
});

const elementProps = {
    label: "Editar Pago",
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
                    route-name="payments.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </SectionTitleLineWithButton>

            <FormPayment
                :payment="payment"
                :users="users"
                :roles="roles"
                :record-statuses="recordStatuses"
                :current-operation="3"
                :element-props="elementProps"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
