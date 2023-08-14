<script setup>
import { mdiTextBoxMultipleOutline, mdiCheckBold } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import Toast from "@/Components/Toast.vue";
import TableSampleAccruals from "@/Components/Accruals/TableSampleAccruals.vue";

// --------------------------------------------
const props = defineProps({
    accruals: Object,
    users: Object,
    roles: Object,
    recordStatuses: Object,
});
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Devengados" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiTextBoxMultipleOutline"
                title="Devengados"
                main
            />

            <Toast
                v-if="$page.props.flash.message"
                :message="$page.props.flash.message"
            />

            <TableSampleAccruals
                :accruals="accruals"
                :users="users"
                :roles="roles"
                :record-statuses="recordStatuses"
                instance="devengado"
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
