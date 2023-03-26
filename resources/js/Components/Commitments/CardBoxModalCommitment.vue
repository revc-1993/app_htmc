<script setup>
import { computed, ref } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import {
    mdiFormatListBulletedType,
    mdiTag,
    mdiDomain,
    mdiNumeric,
    mdiCurrencyUsd,
    mdiCalendarRange,
    mdiBackspaceOutline,
    mdiCardAccountDetails,
} from "@mdi/js";
import CardBoxModal from "@/components/CardBoxModal.vue";
import FormValidationErrors from "@/components/FormValidationErrors.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import Stepper from "@/Components/Stepper.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";
import FormCommitment from "./FormCommitment.vue";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    commitment: {
        type: Object,
        default: {},
    },
    users: Object,
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
                :record-statuses="recordStatuses"
                :current-operation="2"
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
