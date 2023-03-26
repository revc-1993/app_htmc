<script setup>
import { computed, ref } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import {
    mdiFormatListBulletedType,
    mdiTag,
    mdiDomain,
    mdiNumeric,
    mdiCurrencyUsd,
    mdiCardAccountDetails,
} from "@mdi/js";
import CardBoxModal from "@/components/CardBoxModal.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import Stepper from "@/components/Stepper.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";
import FormCertification from "./FormCertification.vue";

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
// CERTIFICATIONS.DELETE
// --------------------------------------------
const destroy = () => {
    router.delete(`/certifications/${props.certification.id}`, {
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
            <FormCertification
                in-modal
                :certification="certification"
                :departments="departments"
                :process-types="processTypes"
                :expense-types="expenseTypes"
                :budget-lines="budgetLines"
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
