<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import CardBoxModal from "@/Components/CardBoxModal.vue";
import FormCommitment from "@/Components/Commitments/FormCommitment.vue";

import { OPERATIONS } from "@/Utils/constants";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    commitment: {
        type: Object,
        default: {},
    },
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

// ---------------------------------------------------------
// EVENTOS DE MODAL: ABRIR Y CERRAR, CONFIRMAR O CANCELAR
// ---------------------------------------------------------
const emit = defineEmits(["update:modelValue", "confirm"]);
const value = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
const confirmCancel = (mode) => {
    value.value = false;
    emit(mode, mode);
};
const confirm = () => confirmCancel("confirm");

// --------------------------------------------
// COMMITMENTS.DELETE
// --------------------------------------------
const destroy = () => {
    router.delete(`/commitments/${props.commitment.id}`, {
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
            <FormCommitment
                in-modal
                :commitment="commitment"
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
