<script setup>
import { computed, ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
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

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    instance: String,
    certification: Object,
    operation: String,
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

// ---------------------------------------------------------
// TITULO Y COLOR DE MODAL
// ---------------------------------------------------------
const title =
    (props.operation === "1"
        ? "Crear "
        : props.operation === "2"
        ? "Ver "
        : props.operation === "3"
        ? "Actualizar "
        : "Eliminar ") + props.instance;
const button =
    props.operation === "1"
        ? "success"
        : props.operation === "2"
        ? "info"
        : props.operation === "3"
        ? "success"
        : "danger";
const disabled = props.operation === "2";

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm(
    props.operation === "1"
        ? {
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
          }
        : {
              contract_object: props.certification.contract_object,
              requesting_area: {
                  id: props.certification.id,
                  label: props.certification.requesting_area,
              },
              amount: props.certification.amount,
              reception_date: props.certification.reception_date,
              assignment_date: props.certification.assignment_date,
              japc_reassignment_date:
                  props.certification.japc_reassignment_date,
              budget_line: {
                  id: props.certification.id,
                  label: props.certification.budget_line,
              },
              process_id: props.certification.process_id,
              certification_number: props.certification.certification_number,
              amount_to_commit: props.certification.amount_to_commit,
              obligation_type: {
                  id: props.certification.id,
                  label: props.certification.obligation_type,
              },
              process_type: {
                  id: props.certification.id,
                  label: props.certification.process_type,
              },
              comments: props.certification.comments,
              user: "",
              returned_document_number:
                  props.certification.returned_document_number,
              management_status: "",
          }
);

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
// const selected = operation === '1' ? "" :

// ---------------------------------------------------------
// CERTIFICATIONS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
        requesting_area: form.requesting_area.label,
    })).post(route("certifications.store"), {
        preserveScroll: false,
        onBefore: () => {
            // console.log("ANTES");
            // console.log(form.data);
        },
        onError: () => {
            // console.log("ERROR");
            // console.log(form.errors);
        },
        onSuccess: () => {
            // console.log("EXITOSO");
            // console.log(form.data);
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
        requesting_area: form.requesting_area.label,
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
            operation === '1'
                ? create()
                : operation === '3'
                ? update()
                : destroy()
        "
    >
        <div v-if="operation !== '4'">
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
                    :placeholder="
                        disabled ? '' : 'Detalle el objeto de contrato'
                    "
                    :has-errors="form.errors.contract_object != null"
                    :disabled="disabled"
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
                        :has-errors="form.errors.requesting_area != null"
                        :disabled="disabled"
                    />
                </FormField>
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
                        :placeholder="disabled ? '' : '1000,00'"
                        :has-errors="form.errors.reception_date != null"
                        :disabled="disabled"
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
                        :placeholder="disabled ? '' : '1000,00'"
                        :has-errors="form.errors.amount != null"
                        :disabled="disabled"
                    />
                </FormField>
            </FormField>

            <FormField>
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
                        :placeholder="disabled ? '' : 'IE-RER-CM-43'"
                        :has-errors="form.errors.certification_number != null"
                        :disabled="disabled"
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
                        :has-errors="form.errors.assignment_date != null"
                        :disabled="disabled"
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
                        :has-errors="form.errors.japc_reassignment_date != null"
                        :disabled="disabled"
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
                        name="budget_line"
                        id="budget_line"
                        :icon="mdiTag"
                        autocomplete="budget_line"
                        :options="selectOptions.budgetLine"
                        :has-errors="form.errors.budget_line != null"
                        :disabled="disabled"
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
                        :placeholder="disabled ? '' : '1001,00'"
                        :has-errors="form.errors.amount_to_commit != null"
                        :disabled="disabled"
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
                        name="obligation_type"
                        id="obligation_type"
                        :icon="mdiFormatListBulletedType"
                        autocomplete="obligation_type"
                        :options="selectOptions.obligationType"
                        :has-errors="form.errors.obligation_type != null"
                        :disabled="disabled"
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
                        name="process_type"
                        id="process_type"
                        :icon="mdiFormatListBulletedType"
                        autocomplete="process_type"
                        :options="selectOptions.proccessType"
                        :has-errors="form.errors.process_type != null"
                        :disabled="disabled"
                    />
                </FormField>
            </FormField>
            <FormField
                label="Observaciones"
                label-for="comments"
                :help="
                    disabled
                        ? ''
                        : 'Indique las principales observaciones. Máximo 255 caracteres.'
                "
                :errors="form.errors.comments"
            >
                <FormControl
                    v-model="form.comments"
                    type="textarea"
                    id="comments"
                    :icon="mdiTag"
                    autocomplete="comments"
                    :has-errors="form.errors.comments != null"
                    :disabled="disabled"
                />
            </FormField>
        </div>
        <div v-else>
            <p>¿Está seguro de continuar con esta acción?</p>
            <p>No se podrá recuperar recuperar los datos eliminados.</p>
        </div>
    </CardBoxModal>
</template>
