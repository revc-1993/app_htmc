<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import CardBoxModal from "@/Components/CardBoxModal.vue";
import FormCertification from "@/Components/Certifications/FormCertification.vue";
import { mdiPrinter } from "@mdi/js";

import { OPERATIONS } from "@/Utils/constants";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    certification: {
        type: Object,
        default: {},
    },
    departments: Object,
    processTypes: Object,
    expenseTypes: Object,
    budgetLines: Object,
    users: Object,
    roles: Object,
    recordStatuses: Object,
    currentOperation: {
        type: [String, Number, Boolean],
        default: OPERATIONS.CREATE,
    },
    elementProps: {
        type: Object,
        default: {
            label: "",
        },
    },
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
});

const certificationSelected = ref(props.certification);

// ---------------------------------------------------------
// EVENTOS DE MODAL: ABRIR Y CERRAR, CONFIRMAR O CANCELAR
// ---------------------------------------------------------
const emit = defineEmits(["update:modelValue", "confirm"]);
const value = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
const confirmCancel = (mode) => {
    certificationSelected.value = {};
    value.value = false;
    emit(mode, mode);
};
const confirm = () => confirmCancel("confirm");

// --------------------------------------------
// CERTIFICATIONS.DELETE
// --------------------------------------------
const destroy = () => {
    router.delete(`/certifications/${props.certification.id}`, {
        onSuccess: () => confirm(),
    });
};

const transaction = () => {
    return props.currentOperation === OPERATIONS.DELETE ? destroy() : "";
};
</script>

<template>
    <CardBoxModal
        v-model="value"
        :title="elementProps.label"
        :button="
            props.currentOperation === OPERATIONS.UPDATE
                ? 'success'
                : elementProps.color
        "
        :button-label="
            props.currentOperation === OPERATIONS.SHOW
                ? 'Imprimir'
                : elementProps.label
        "
        has-cancel
        @confirm="transaction"
    >
        <template v-if="currentOperation !== OPERATIONS.DELETE">
            <FormCertification
                in-modal
                :certification="certification"
                :departments="departments"
                :process-types="processTypes"
                :expense-types="expenseTypes"
                :budget-lines="budgetLines"
                :users="users"
                :roles="roles"
                :record-statuses="recordStatuses"
                :current-operation="currentOperation"
                :element-props="elementProps"
                :with-button="
                    currentOperation === OPERATIONS.CREATE ||
                    currentOperation === OPERATIONS.UPDATE
                "
            />
        </template>
        <template v-else>
            <h3><b>¿Está seguro de continuar con esta acción?</b></h3>
            <div>
                Nota: No se podrá recuperar recuperar los datos eliminados.
            </div>
        </template>
    </CardBoxModal>
</template>
