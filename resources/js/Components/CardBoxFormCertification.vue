<script setup>
import {
    mdiCardAccountDetails,
    mdiFormatListBulletedType,
    mdiTag,
    mdiDomain,
    mdiNumeric,
    mdiCurrencyUsd,
    mdiCalendarRange,
    mdiBackspaceOutline,
    mdiContentSave,
} from "@mdi/js";
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import CardBox from "@/components/CardBox.vue";
import FormValidationErrors from "@/components/FormValidationErrors.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";

const props = defineProps({
    title: String,
    operation: String,
    id: Number,
    certification: Object,
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
});

// console.log(props.operation);
// console.log(props.id);
// console.log(props.certification);
// console.log(props.certifications);

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
// if (props.operation === "1") {
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
});
// } else {
//     const form = useForm({
//         contract_object: props.certification.contract_object,
//         requesting_area: props.certification.requesting_area,
//         amount: props.certification.amount,
//         reception_date: props.certification.reception_date,
//         assignment_date: props.certification.assignment_date,
//         japc_reassignment_date: props.certification.japc_reassignment_date,
//         budget_line: props.certification.budget_line,
//         process_id: props.certification.process_id,
//         certification_number: props.certification.certification_number,
//         amount_to_commit: props.certification.amount_to_commit,
//         obligation_type: props.certification.obligation_type,
//         process_type: props.certification.process_type,
//         comments: props.certification.comments,
//         user: "",
//         returned_document_number: props.certification.returned_document_number,
//         management_status: "",
//     });
// }

// ---------------------------------------------------------
// SELECTS
// ---------------------------------------------------------
const selectOptions = {
    requestingArea: [
        { id: 1, label: "Despacho" },
        { id: 2, label: "Financiero" },
        { id: 3, label: "Tesorería" },
    ],
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
    proccessType: [
        { id: 1, label: "Proceso 1" },
        { id: 2, label: "Proceso 2" },
        { id: 3, label: "Proceso 3" },
    ],
};

// ---------------------------------------------------------
// certifications.store
// ---------------------------------------------------------
// const hasErrors = ref(false);
const create = () => {
    form.transform((data) => ({
        ...data,
        requesting_area: form.requesting_area.label,
    })).post(route("certifications.store"), {
        preserveScroll: false,
        onBefore: () => console.log(form.data),
        onSuccess: () => {
            form.reset();
            confirm();
        },
    });
};

const update = () => {
    form.transform((data) => ({
        ...data,
        requesting_area: form.requesting_area.label,
    })).put(route("certifications.store", props.id), {
        preserveScroll: false,
        onSuccess: () => {
            confirm();
        },
    });
};

// ---------------------------------------------------------
// EVENTOS DE CERRAR MODAL: CONFIRMAR O CANCELAR
// ---------------------------------------------------------
const emit = defineEmits(["update:modelValue", "cancel", "confirmed"]);
const value = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
const confirmCancel = (mode) => {
    value.value = false;
    emit(mode);
};
const confirm = () => confirmCancel("confirmed");
const cancel = () => confirmCancel("cancel");
</script>

<template>
    <CardBox
        is-form
        @submit.prevent="
            operation === '1' ? create() : operation === '2' ? show() : update()
        "
    >
        <FormValidationErrors v-if="form.hasErrors" />
        <FormField
            label="Objeto de contrato"
            label-for="contract_object"
            :errors="form.errors.contract_object"
        >
            <FormControl
                v-model="form.contract_object"
                id="contract_object"
                :icon="mdiCardAccountDetails"
                autocomplete="contract_object"
                type="text"
                placeholder="Detalle el objeto de contrato"
                :has-errors="form.errors.contract_object"
            />
        </FormField>
        <FormField>
            <FormField
                label="Area requirente"
                label-for="requesting_area"
                help="Seleccione el área requirente"
                :errors="form.errors.requesting_area"
            >
                <FormControl
                    v-model="form.requesting_area"
                    name="requesting_area"
                    id="requesting_area"
                    :icon="mdiDomain"
                    autocomplete="requesting_area"
                    :options="selectOptions.requestingArea"
                    :has-errors="form.errors.requesting_area"
                />
            </FormField>
            <FormField
                label="N. Certificación"
                label-for="certification_number"
                help="Ingrese el Nro. de la certificación"
                :errors="form.errors.certification_number"
            >
                <FormControl
                    v-model="form.certification_number"
                    id="certification_number"
                    :icon="mdiNumeric"
                    autocomplete="certification_number"
                    type="text"
                    placeholder="IE-RER-CM-43"
                    :has-errors="form.errors.certification_number"
                />
            </FormField>
            <FormField
                label="Monto"
                label-for="amount"
                help="Ingrese el monto de la certificación"
                :errors="form.errors.amount"
            >
                <FormControl
                    v-model="form.amount"
                    id="amount"
                    :icon="mdiCurrencyUsd"
                    autocomplete="amount"
                    type="text"
                    inputmode="decimal"
                    placeholder="1000,00"
                    :has-errors="form.errors.amount"
                />
            </FormField>
        </FormField>

        <FormField>
            <FormField
                label="Fecha de recepción"
                label-for="reception_date"
                help="Ingrese la fecha de recepción"
                :errors="form.errors.reception_date"
            >
                <FormControl
                    v-model="form.reception_date"
                    id="reception_date"
                    :icon="mdiCalendarRange"
                    autocomplete="reception_date"
                    type="date"
                    placeholder="1000,00"
                    :has-errors="form.errors.reception_date"
                />
            </FormField>
            <FormField
                label="Fecha de asignado"
                label-for="assignment_date"
                help="Ingrese la fecha de asignación"
                :errors="form.errors.assignment_date"
            >
                <FormControl
                    v-model="form.assignment_date"
                    id="assignment_date"
                    :icon="mdiCalendarRange"
                    autocomplete="assignment_date"
                    type="date"
                    :has-errors="form.errors.assignment_date"
                />
            </FormField>
            <FormField
                label="Fecha reasignado JAPC"
                label-for="japc_reassignment_date"
                help="Ingrese la fecha de reasignación JAPC"
                :errors="form.errors.japc_reassignment_date"
            >
                <FormControl
                    v-model="form.japc_reassignment_date"
                    id="japc_reassignment_date"
                    :icon="mdiCalendarRange"
                    autocomplete="japc_reassignment_date"
                    type="date"
                    :has-errors="form.errors.japc_reassignment_date"
                />
            </FormField>
        </FormField>

        <FormField>
            <FormField
                label="Item presupuestario"
                label-for="budget_line"
                help="Escoja el ítem presupuestario"
                :errors="form.errors.budget_line"
            >
                <FormControl
                    v-model="form.budget_line"
                    id="budget_line"
                    :icon="mdiTag"
                    autocomplete="budget_line"
                    :options="selectOptions.budgetLine"
                    :has-errors="form.errors.budget_line"
                />
            </FormField>
            <FormField
                label="Monto a comprometer"
                label-for="amount_to_commit"
                help="Ingrese el monto a comprometer"
                :errors="form.errors.amount_to_commit"
            >
                <FormControl
                    v-model="form.amount_to_commit"
                    id="amount_to_commit"
                    :icon="mdiCurrencyUsd"
                    autocomplete="amount_to_commit"
                    type="number"
                    placeholder="1001,00"
                    :has-errors="form.errors.amount_to_commit"
                />
            </FormField>
        </FormField>
        <FormField>
            <FormField
                label="Tipo de obligación"
                label-for="obligation_type"
                help="Escoja el tipo de obligación"
                :errors="form.errors.obligation_type"
            >
                <FormControl
                    v-model="form.obligation_type"
                    id="obligation_type"
                    :icon="mdiFormatListBulletedType"
                    autocomplete="obligation_type"
                    :options="selectOptions.obligationType"
                    :has-errors="form.errors.obligation_type"
                />
            </FormField>
            <FormField
                label="Tipo de proceso"
                label-for="process_type"
                help="Escoja el tipo de proceso"
                :errors="form.errors.process_type"
            >
                <FormControl
                    v-model="form.process_type"
                    id="process_type"
                    :icon="mdiFormatListBulletedType"
                    autocomplete="process_type"
                    :options="selectOptions.proccessType"
                    :has-errors="form.errors.process_type"
                />
            </FormField>
        </FormField>

        <BaseButtons>
            <BaseButton
                type="submit"
                :label="
                    (operation === '1'
                        ? 'Crear '
                        : operation === '2'
                        ? 'Ver '
                        : 'Actualizar ') + title
                "
                color="success"
                :icon="mdiCheckBold"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            />
            <BaseButton
                label="Cancelar"
                color="lightDark"
                :icon="mdiBackspaceOutline"
                @click="cancel"
            />
        </BaseButtons>
    </CardBox>
</template>
