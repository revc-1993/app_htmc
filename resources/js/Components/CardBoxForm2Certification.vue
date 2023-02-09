<script setup>
import {
    mdiCardAccountDetails,
    mdiFormatListBulletedType,
    mdiTag,
    mdiDomain,
    mdiNumeric,
    mdiCurrencyUsd,
    mdiCalendarRange,
    mdiComment,
    mdiCardBulleted,
} from "@mdi/js";
import { computed } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import CardBoxModal from "@/components/CardBoxModal.vue";
import FormValidationErrors from "@/components/FormValidationErrors.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";

const props = defineProps({
    modal: String,
    title: String,
    color: String,
    operation: String,
    modalActive: Object,
});

const form = useForm({
    contract_object: "",
    requesting_area: "",
    amount: "",
    reception_date: "",
    assignment_date: "",
    japc_reassignment_date: "",
    budget_line: "",
    process_id: "",
    certification_number: "",
    amount_to_commit: "",
    obligation_type: "",
    process_type: "",
    comments: "",
    user: "",
    returned_document_number: "",
    management_status: "",
    remember: [],
});

const selectOptionsRequestingArea = [
    { id: 1, label: "Despacho" },
    { id: 2, label: "Financiero" },
    { id: 3, label: "Tesorería" },
];

const selectOptionsBudgetLine = [
    { id: 1, label: "530073 - Item 1" },
    { id: 2, label: "730010 - Item 2" },
    { id: 3, label: "400000 - Item 3" },
];

const selectOptionsObligationType = [
    { id: 1, label: "Obligación 1" },
    { id: 2, label: "Obligación 2" },
    { id: 3, label: "Obligación 3" },
];

const selectOptionsProccessType = [
    { id: 1, label: "Proceso 1" },
    { id: 2, label: "Proceso 2" },
    { id: 3, label: "Proceso 3" },
];

// const save = () => {
//     form.transform((data) => ({ ...data })).post(
//         route("certifications.store"),
//         { onFinish: () => form.reset() }
//     );
// };

// const submit = () => {
//     form.transform((data) => ({ ...data })).post(
//         route("certifications.store"),
//         { onFinish: () => form.reset() }
//     );
// };

// ----------------------------------------------------------------------
// const emit = defineEmits(["update:modelValue", "cancel", "confirm"]);

// const value = computed({
//     get: () => props.modelValue,
//     set: (value) => emit("update:modelValue", value),
// });

// const confirmCancel = (mode) => {
//     value.value = false;
//     emit(mode);
// };

// const confirm = () => confirmCancel("confirm");

// const cancel = () => confirmCancel("cancel");

// window.addEventListener("keydown", (e) => {
//     if (e.key === "Escape" && value.value) {
//         cancel();
//     }
// });
// ----------------------------------------------------------------------
</script>

<template>
    <CardBoxModal
        :title="title"
        :button="color"
        :button-label="
            operation === '1'
                ? 'Crear certificación'
                : 'Actualizar certificación'
        "
        has-cancel
        v-model:modalActive="modalActive"
        v-model:formProcessing="form.processing"
        is-form
        @submit.prevent="operation === '1' ? save() : update()"
    >
        <FormValidationErrors />

        <FormField label="Objeto de contrato" label-for="contract_object">
            <FormControl
                v-model="form.contract_object"
                id="contract_object"
                :icon="mdiCardAccountDetails"
                autocomplete="contract_object"
                type="text"
                required
                placeholder="Detalle el objeto de contrato"
            />
        </FormField>

        <FormField>
            <FormField
                label="Area requirente"
                label-for="requesting_area"
                help="Seleccione el área requirente"
            >
                <FormControl
                    v-model="form.requesting_area"
                    id="requesting_area"
                    :icon="mdiDomain"
                    autocomplete="requesting_area"
                    :options="selectOptionsRequestingArea"
                    required
                />
            </FormField>
            <FormField
                label="Nro. Certificación"
                label-for="certification_number"
                help="Ingrese el Nro. de la certificación"
            >
                <FormControl
                    v-model="form.certification_number"
                    id="certification_number"
                    :icon="mdiNumeric"
                    autocomplete="certification_number"
                    type="text"
                    placeholder="IE-RER-CM-43"
                    required
                />
            </FormField>
            <FormField
                label="Monto"
                label-for="amount"
                help="Ingrese el monto de la certificación"
            >
                <FormControl
                    v-model="form.amount"
                    id="amount"
                    :icon="mdiCurrencyUsd"
                    autocomplete="amount"
                    type="text"
                    inputmode="decimal"
                    placeholder="1000,00"
                    required
                />
            </FormField>
        </FormField>

        <!-- <BaseDivider /> -->

        <FormField>
            <FormField
                label="Fecha de recepción"
                label-for="reception_date"
                help="Ingrese la fecha de recepción"
            >
                <FormControl
                    v-model="form.reception_date"
                    id="reception_date"
                    :icon="mdiCalendarRange"
                    autocomplete="reception_date"
                    type="date"
                    placeholder="1000,00"
                />
            </FormField>
            <FormField
                label="Fecha de asignado"
                label-for="assignment_date"
                help="Ingrese la fecha de asignación"
            >
                <FormControl
                    v-model="form.assignment_date"
                    id="assignment_date"
                    :icon="mdiCalendarRange"
                    autocomplete="assignment_date"
                    type="date"
                />
            </FormField>
            <FormField>
                <FormField
                    label="Fecha de reasignación JAPC"
                    label-for="japc_reassignment_date"
                    help="Ingrese la fecha de reasignación JAPC"
                >
                    <FormControl
                        v-model="form.japc_reassignment_date"
                        id="japc_reassignment_date"
                        :icon="mdiCalendarRange"
                        autocomplete="japc_reassignment_date"
                        type="date"
                    />
                </FormField>
            </FormField>
        </FormField>

        <FormField>
            <FormField
                label="Item presupuestario"
                label-for="budget_line"
                help="Escoja el ítem presupuestario"
            >
                <FormControl
                    v-model="form.budget_line"
                    id="budget_line"
                    :icon="mdiTag"
                    autocomplete="budget_line"
                    :options="selectOptionsBudgetLine"
                />
            </FormField>
            <FormField
                label="Monto a comprometer"
                label-for="amount_to_commit"
                help="Ingrese el monto a comprometer"
            >
                <FormControl
                    v-model="form.amount_to_commit"
                    id="amount_to_commit"
                    :icon="mdiCurrencyUsd"
                    autocomplete="amount_to_commit"
                    type="number"
                    placeholder="1001,00"
                />
            </FormField>
        </FormField>
        <FormField>
            <FormField
                label="Tipo de obligación"
                label-for="obligation_type"
                help="Escoja el tipo de obligación"
            >
                <FormControl
                    v-model="form.obligation_type"
                    id="obligation_type"
                    :icon="mdiFormatListBulletedType"
                    autocomplete="obligation_type"
                    :options="selectOptionsObligationType"
                />
            </FormField>
            <FormField
                label="Tipo de proceso"
                label-for="process_type"
                help="Escoja el tipo de proceso"
            >
                <FormControl
                    v-model="form.process_type"
                    id="process_type"
                    :icon="mdiFormatListBulletedType"
                    autocomplete="process_type"
                    :options="selectOptionsProccessType"
                />
            </FormField>
        </FormField>
    </CardBoxModal>
</template>
