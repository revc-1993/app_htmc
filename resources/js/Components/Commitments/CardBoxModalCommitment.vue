<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import CardBoxModal from "@/Components/CardBoxModal.vue";
import FormCommitment from "@/Components/Commitments/FormCommitment.vue";

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
        default: 1,
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
// CONSTANTES
// ---------------------------------------------------------
const operations = {
    create: 1,
    show: 2,
    update: 3,
    delete: 4,
};

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
    return props.currentOperation === operations.delete ? destroy() : "";
};
</script>

<template>
    <CardBoxModal
        v-model="value"
        :title="elementProps.label"
        :button="
            props.currentOperation === operations.update
                ? 'success'
                : elementProps.color
        "
        :button-label="
            props.currentOperation === operations.show
                ? 'Imprimir'
                : elementProps.label
        "
        has-cancel
        @confirm="transaction"
    >
        <template v-if="currentOperation !== operations.delete">
            <FormCommitment
                in-modal
                :commitment="commitment"
                :users="users"
                :roles="roles"
                :record-statuses="recordStatuses"
                :current-operation="currentOperation"
                :element-props="elementProps"
                :with-button="
                    currentOperation === operations.create ||
                    currentOperation === operations.update
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
