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
import StepperState from "@/components/StepperState.vue";
import Stepper from "@/components/Stepper.vue";
import Step from "@/components/Step.vue";
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
});

const isNotRegister = computed(() => {
    typeof props.certification.management_status !== "undefined";
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
    {
        1: "Crear ",
        2: "Ver ",
        3: "Actualizar ",
        4: "Eliminar ",
    }[props.operation] + props.instance;

const button = computed(() => {
    return {
        1: "success",
        2: "info",
        3: "success",
        4: "danger",
    }[props.operation];
});

const isCreate = props.operation === "1";
const isShow = props.operation === "2";
const isUpdate = props.operation === "3";

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
// FORM
// ---------------------------------------------------------
const idOptionSelect = (arrayOptions, value) => {
    if (value && typeof value !== "undefined")
        // return arrayOptions.filter((option) => option.label == value);
        return arrayOptions.find((option) => option.id == value).label || "";
    else return "";
};

const form = useForm(
    props.operation === "1"
        ? {
              contract_object: "",
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
              customer_id: usePage().props.auth.user.id,
              department_id: "",
              returned_document_number: "",
              last_validation: "",
          }
        : {
              contract_object: props.certification.contract_object,
              amount: props.certification.amount,
              reception_date: props.certification.reception_date,
              assignment_date: props.certification.assignment_date,
              japc_reassignment_date:
                  props.certification.japc_reassignment_date,
              budget_line: {
                  id: props.certification.budget_line,
                  label: idOptionSelect(
                      selectOptions.budgetLine,
                      props.certification.budget_line
                  ),
              },
              process_id: props.certification.process_id,
              certification_number: props.certification.certification_number,
              amount_to_commit: props.certification.amount_to_commit,
              obligation_type: {
                  id: props.certification.obligation_type,
                  label: idOptionSelect(
                      selectOptions.obligationType,
                      props.certification.obligation_type
                  ),
              },
              process_type: {
                  id: props.certification.process_type,
                  label: idOptionSelect(
                      selectOptions.processType,
                      props.certification.process_type
                  ),
              },
              comments: props.certification.comments,
              customer_id: usePage().props.auth.user.id,
              department_id: {
                  id: props.certification.department_id,
                  label: idOptionSelect(
                      selectOptions.requestingArea,
                      props.certification.department_id
                  ),
              },
              returned_document_number:
                  props.certification.returned_document_number,
              last_validation: props.certification.last_validation,
          }
);

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
            operation === '1'
                ? create()
                : operation === '3'
                ? update()
                : destroy()
        "
    >
        <div v-if="operation !== '4'">
            <FormValidationErrors v-if="form.hasErrors" />

            <Step
                v-if="value"
                :state="isCreate ? '' : certification.management_status"
            />
            <BaseDivider />

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
                    :has-errors="form.errors.contract_object != null"
                    :disabled="isShow"
                />
            </FormField>
            <FormField>
                <FormField
                    label="Area requirente"
                    label-for="department_id"
                    help="Seleccione el área requirente"
                    :errors="form.errors.department_id"
                >
                    <FormControl
                        v-model="form.department_id"
                        name="department_id"
                        id="department_id"
                        :icon="mdiDomain"
                        autocomplete="department_id"
                        :options="selectOptions.requestingArea"
                        :has-errors="form.errors.department_id != null"
                        :disabled="isShow"
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
                        placeholder="1000,00"
                        :has-errors="form.errors.reception_date != null"
                        :disabled="isShow"
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
                        type="number"
                        inputmode="decimal"
                        placeholder="Ej: 1000.00"
                        :step="0.0001"
                        :min="0"
                        :has-errors="form.errors.amount != null"
                        :disabled="isShow"
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
                        :disabled="isShow"
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
                        :disabled="isShow"
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
                        :disabled="isShow"
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
                        :disabled="isShow"
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
                        :options="selectOptions.processType"
                        :has-errors="form.errors.process_type != null"
                        :disabled="isShow"
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
                        placeholder="Ej: IE-RER-CM-43"
                        :has-errors="form.errors.certification_number != null"
                        :disabled="isShow"
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
                        inputmode="decimal"
                        placeholder="Ej: 1000.00"
                        :step="0.0001"
                        :has-errors="form.errors.amount_to_commit != null"
                        :disabled="isShow"
                    />
                </FormField>
            </FormField>
            <FormField
                label="Validación de Tesorería"
                label-for="last_validation"
                :errors="form.errors.last_validation"
            >
                <FormCheckRadioGroup
                    v-model="form.last_validation"
                    name="last_validation"
                    :disabled="isShow"
                    :options="{
                        agree: 'La certificación se encuentra correctamente registrada.',
                    }"
                />
            </FormField>
            <FormField
                label="Observaciones"
                label-for="comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.comments"
            >
                <FormControl
                    v-model="form.comments"
                    type="textarea"
                    id="comments"
                    :icon="mdiTag"
                    autocomplete="comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.comments != null"
                    :disabled="isShow"
                />
            </FormField>
        </div>
        <div v-else>
            <p>¿Está seguro de continuar con esta acción?</p>
            <p>No se podrá recuperar recuperar los datos eliminados.</p>
        </div>
    </CardBoxModal>
</template>
