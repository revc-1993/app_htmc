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
    process_types: Object,
    expense_types: Object,
    users: Object,
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
// STEPPER Y ROLES
// ---------------------------------------------------------
const role = usePage().props.auth.user.roles[0].id;
const activePhase = ref(1);
activePhase.value === role ? role : 1;

// ---------------------------------------------------------
// TRANSACCION A REALIZAR
// ---------------------------------------------------------
const transaction = () => {
    if (props.operation === "1") {
        create();
    } else if (props.operation === "3") {
        update();
    } else if (props.operation === "4") {
        destroy();
    } else {
        console.log("Error de envío de formulario");
    }
};

// ---------------------------------------------------------
// CARACTERISTICAS DE MODAL
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

const disabled = computed(() => {
    return props.operation === "2" || activePhase.value !== role;
});

// ---------------------------------------------------------
// SELECTS
// ---------------------------------------------------------
let process_types = [];
let expense_types = [];
let departments = [];
let users = [];

const optionSelect = (array, newArray) => {
    array.forEach((element) => {
        newArray.push({
            id: Object.values(element)[0],
            label: Object.values(element)[1],
        });
    });
    return newArray;
};

let selectOptions = {
    processType: optionSelect(props.process_types, process_types),
    expenseType: optionSelect(props.expense_types, expense_types),
    requestingArea: optionSelect(props.departments, departments),
    users: optionSelect(props.users, users),
};

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm(
    props.operation === "1"
        ? {
              certification_memo: "",
              content: "",
              contract_object: "",
              process_type_id: "",
              expense_type_id: "",
              department_id: "",
              cgf_comments: "",
              customer_id: "",
              //   assignment_date: "",
              //   japc_reassignment_date: "",
              //   budget_line: "",
              //   process_id: "",
              //   certification_number: "",
              //   amount_to_commit: "",
              //   obligation_type: "",
              //   comments: "",
              //   customer_id: usePage().props.auth.user.id,
              //   returned_document_number: "",
              //   last_validation: "",
          }
        : {
              certification_memo: props.certification.certification_memo,
              content: props.certification.content,
              contract_object: props.certification.contract_object,
              process_type_id: props.certification.process_type_id,
              expense_type_id: props.certification.expense_type_id,
              department_id: props.certification.department_id,
              cgf_comments: props.certification.cgf_comments,
              customer_id:
                  role.value === "1" ? props.certification.customer_id : "",
              //   assignment_date: props.certification.assignment_date,
              //   japc_reassignment_date:
              //       props.certification.japc_reassignment_date,
              //   budget_line: {
              //       id: props.certification.budget_line,
              //       label: idOptionSelect(
              //           selectOptions.budgetLine,
              //           props.certification.budget_line
              //       ),
              //   },
              //   process_id: props.certification.process_id,
              //   certification_number: props.certification.certification_number,
              //   amount_to_commit: props.certification.amount_to_commit,
              //   obligation_type: {
              //       id: props.certification.obligation_type,
              //       label: idOptionSelect(
              //           selectOptions.obligationType,
              //           props.certification.obligation_type
              //       ),
              //   },
              //   customer_id: usePage().props.auth.user.id,
              //   department_id: {
              //       id: props.certification.department_id,
              //       label: idOptionSelect(
              //           selectOptions.requestingArea,
              //           props.certification.department_id
              //       ),
              //   },
              //   returned_document_number:
              //       props.certification.returned_document_number,
              //   last_validation: props.certification.last_validation,
          }
);

// ---------------------------------------------------------
// CERTIFICATIONS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
    })).post(route("certifications.store"), {
        preserveScroll: false,
        onStart: () => console.log("CREATE"),
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
    })).put(route("certifications.update", props.certification.id), {
        preserveScroll: false,
        onStart: () => console.log("UPDATE"),
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
        @confirm="transaction"
    >
        <template v-if="operation !== '4'">
            <Stepper v-model="activePhase" />

            <!-- STEP 1 -->
            <div v-show="activePhase === 1">
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
                        :disabled="disabled"
                    />
                </FormField>
                <FormField>
                    <FormField
                        label="Nro. Memorando de certificación"
                        label-for="certification_memo"
                        help="Ingrese el Nro. de Memorando de la certificación"
                        :errors="form.errors.certification_memo"
                    >
                        <FormControl
                            v-model="form.certification_memo"
                            id="certification_memo"
                            :icon="mdiNumeric"
                            autocomplete="certification_memo"
                            type="text"
                            placeholder="Ej: IE-RER-CM-43"
                            :has-errors="form.errors.certification_memo != null"
                            :disabled="disabled"
                        />
                    </FormField>
                    <FormField
                        label="Contenido"
                        label-for="content"
                        help="Indique el contenido de la certificación"
                        :errors="form.errors.content"
                    >
                        <FormControl
                            v-model="form.content"
                            id="content"
                            :icon="mdiNumeric"
                            autocomplete="content"
                            type="text"
                            placeholder="Ej: 01 EXPEDIENTE CON VINCHA"
                            :has-errors="form.errors.content != null"
                            :disabled="disabled"
                        />
                    </FormField>
                </FormField>
                <FormField>
                    <FormField
                        label="Tipo de proceso"
                        label-for="process_type_id"
                        help="Escoja el tipo de proceso"
                        :errors="form.errors.process_type_id"
                    >
                        <FormControl
                            v-model="form.process_type_id"
                            name="process_type_id"
                            id="process_type_id"
                            :icon="mdiFormatListBulletedType"
                            autocomplete="process_type_id"
                            :options="selectOptions.processType"
                            :has-errors="form.errors.process_type_id != null"
                            :disabled="disabled"
                        />
                    </FormField>
                    <FormField
                        label="Tipo de gasto"
                        label-for="expense_type_id"
                        help="Escoja el tipo de gasto"
                        :errors="form.errors.expense_type_id"
                    >
                        <FormControl
                            v-model="form.expense_type_id"
                            name="expense_type_id"
                            id="expense_type_id"
                            :icon="mdiFormatListBulletedType"
                            autocomplete="expense_type_id"
                            :options="selectOptions.expenseType"
                            :has-errors="form.errors.expense_type_id != null"
                            :disabled="disabled"
                        />
                    </FormField>
                </FormField>
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
                        :disabled="disabled"
                    />
                </FormField>
                <FormField
                    label="Observaciones"
                    label-for="cgf_comments"
                    help="Máximo 255 caracteres."
                    :errors="form.errors.cgf_comments"
                >
                    <FormControl
                        v-model="form.cgf_comments"
                        type="textarea"
                        id="cgf_comments"
                        :icon="mdiTag"
                        autocomplete="cgf_comments"
                        placeholder="Indique las principales observaciones."
                        :has-errors="form.errors.cgf_comments != null"
                        :disabled="disabled"
                    />
                </FormField>
            </div>
            <!-- STEP 2 -->
            <div v-show="activePhase === 2">
                <FormField>
                    <FormField
                        label="Contenido"
                        label-for="content"
                        help="Indique el contenido de la certificación"
                        :errors="form.errors.content"
                    >
                        <FormControl
                            v-model="form.content"
                            id="content"
                            :icon="mdiNumeric"
                            autocomplete="content"
                            type="text"
                            placeholder="Ej: 01 EXPEDIENTE CON VINCHA"
                            :has-errors="form.errors.content != null"
                            :disabled="disabled"
                        />
                    </FormField>
                    <FormField
                        label="Reasignar a "
                        label-for="customer_id"
                        help="Seleccione el usuario a reasignar la gestión"
                        :errors="form.errors.customer_id"
                    >
                        <FormControl
                            v-model="form.customer_id"
                            name="customer_id"
                            id="customer_id"
                            :icon="mdiDomain"
                            autocomplete="customer_id"
                            :options="selectOptions.users"
                            :has-errors="form.errors.customer_id != null"
                            :disabled="disabled"
                        />
                    </FormField>
                </FormField>
                <FormField
                    label="Observaciones"
                    label-for="cgf_comments"
                    help="Máximo 255 caracteres."
                    :errors="form.errors.cgf_comments"
                >
                    <FormControl
                        v-model="form.cgf_comments"
                        type="textarea"
                        id="cgf_comments"
                        :icon="mdiTag"
                        autocomplete="cgf_comments"
                        placeholder="Indique las principales observaciones."
                        :has-errors="form.errors.cgf_comments != null"
                        :disabled="disabled"
                    />
                </FormField>
            </div>
            <!-- STEP 3 -->
            <div v-show="activePhase === 3"></div>
            <!-- STEP 4 -->
            <div v-show="activePhase === 4"></div>
        </template>
        <template v-else>
            <strong>¿Está seguro de continuar con esta acción?</strong>
            <p>No se podrá recuperar recuperar los datos eliminados.</p>
        </template>
    </CardBoxModal>
</template>
