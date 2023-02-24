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
import Stepper from "@/components/Stepper.vue";
import StepperComponent from "@/components/StepperComponent.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    instance: String,
    certification: Object,
    operation: String,
    departments: Object,
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
    status: String,
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

// ---------------------------------------------------------
// TITULO Y COLOR DE MODAL
// ---------------------------------------------------------
const title = computed(() => {
    return (
        {
            1: "Crear ",
            2: "Ver ",
            3: "Actualizar ",
            4: "Eliminar ",
        }[props.operation] + props.instance
    );
});

const button = computed(() => {
    return {
        1: "success",
        2: "info",
        3: "success",
        4: "danger",
    }[props.operation];
});

const isCreate = computed(() => {
    props.operation === "1";
});
const isShow = computed(() => {
    props.operation === "2";
});
const isUpdate = computed(() => {
    props.operation === "3";
});
const isDelete = computed(() => {
    props.operation === "4";
});

// ---------------------------------------------------------
// SELECTS
// ---------------------------------------------------------

let departments = [];
props.departments.forEach((element) => {
    departments.push({ id: element.id, label: element.department });
});

let selectOptions = {
    requestingArea: departments,
    budgetLine: [
        { id: 1, label: "530073 - Item 1" },
        { id: 2, label: "730010 - Item 2" },
        { id: 3, label: "400000 - Item 3" },
    ],
    obligationType: [
        { id: 1, label: "Obligación 1" },
        { id: 2, label: "Obligación 2" },
        { id: 3, label: "Obligación 3" },
    ],
    processType: [
        { id: 1, label: "Proceso 1" },
        { id: 2, label: "Proceso 2" },
        { id: 3, label: "Proceso 3" },
    ],
};

// ---------------------------------------------------------
// CERTIFICATIONS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
        department_id: form.department_id.id,
        customer_id: usePage().props.auth.user.id,
        budget_line: form.budget_line.id,
        obligation_type: form.obligation_type.id,
        process_type: form.process_type.id,
    })).post(route("certifications.store"), {
        preserveScroll: false,
        onSuccess: () => {
            form.reset();
            confirm();
        },
    });
};

// ---------------------------------------------------------
// CERTIFICATIONS.UPDATE
// ---------------------------------------------------------
const update = () => {
    form.transform((data) => ({
        ...data,
        department_id: form.department_id.id,
        customer_id: usePage().props.auth.user.id,
        budget_line: form.budget_line.id,
        obligation_type: form.obligation_type.id,
        process_type: form.process_type.id,
    })).put(route("certifications.update", props.certification.id), {
        preserveScroll: false,
        onSuccess: () => {
            form.reset();
            confirm();
        },
    });
};

// --------------------------------------------
// CERTIFICATIONS.DELETE
// --------------------------------------------
const destroy = () => {
    router.delete(`/certifications/${props.certification.id}`, {
        onSuccess: () => confirm(),
    });
};
</script>

<template>
    <CardBoxModal
        v-model="value"
        :title="title"
        :button="button"
        :button-label="title"
        has-cancel
        is-form
        @confirm="
            isCreate
                ? create()
                : isUpdate
                ? update()
                : isDelete
                ? destroy()
                : ''
        "
    >
        <Stepper />
    </CardBoxModal>
</template>
