<script setup>
import { computed, ref, onMounted } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import {
    mdiFormatListBulletedType,
    mdiTag,
    mdiPrinter,
    mdiDomain,
    mdiNumeric,
    mdiCurrencyUsd,
    mdiCardAccountDetails,
    mdiContentSaveAll,
    mdiBackspace,
} from "@mdi/js";
import CardBox from "@/components/CardBox.vue";
import LabelDate from "@/Components/LabelDate.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import FormControlBudgetLine from "@/Components/FormControlBudgetLine.vue";
import Stepper from "@/Components/Stepper.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import axios from "axios";
import FormValidationErrors from "@/Components/FormValidationErrors.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseLevel from "@/components/BaseLevel.vue";

import { ROLES, STEPS, STATUSES, OPERATIONS } from "@/Utils/constants";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime"; // Importa el complemento de fechas relativas
import "dayjs/locale/es"; // Si deseas utilizar el idioma español

dayjs.locale("es");
dayjs.extend(relativeTime);

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
    withButton: {
        type: Boolean,
        default: true,
    },
    inModal: {
        type: Boolean,
        default: false,
    },
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
});

// ---------------------------------------------------------
// PROPIEDADES
// ---------------------------------------------------------
const role = usePage().props.auth.user.roles;
const activePhase = ref(STEPS.STEP_1);

// ---------------------------------------------------------
// SETS
// ---------------------------------------------------------
activePhase.value =
    role.name !== ROLES.ADMIN
        ? role.step
        : props.certification &&
          props.certification.current_management &&
          props.currentOperation === OPERATIONS.SHOW
        ? props.certification.current_management.step
        : props.certification &&
          props.certification.current_management &&
          props.currentOperation === OPERATIONS.UPDATE
        ? props.certification.current_management
        : STEPS.STEP_1;

// ---------------------------------------------------------
// SELECTS
// ---------------------------------------------------------
const optionSelect = (array = []) => {
    let newArray = [];
    array.forEach((element) => {
        newArray.push({
            id: Object.values(element)[0],
            label: Object.values(element)[1],
        });
    });
    return newArray;
};

const optionSelect2 = (array = []) => {
    let newArray = [];
    array.forEach((element) => {
        newArray.push({
            id: Object.values(element)[0],
            label:
                Object.values(element)[1] + " - " + Object.values(element)[2],
        });
    });
    return newArray;
};

const optionSelect3 = (array = []) => {
    let newArray = [];
    array.forEach((element) => {
        newArray.push({
            id: Object.values(element)[0],
            label: Object.values(element)[1],
            description: Object.values(element)[2],
        });
    });
    return newArray;
};

let selectOptions = {
    requestingArea: optionSelect2(props.departments),
    processType: optionSelect(props.processTypes),
    expenseType: optionSelect(props.expenseTypes),
    budgetLine: optionSelect3(props.budgetLines),
    users: optionSelect(props.users),
    recordStatus: optionSelect(props.recordStatuses),
};

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm({
    certification_memo: "",
    content: "",
    contract_object: "",
    process_type_id: "",
    expense_type_id: "",
    department_id: "",
    sec_cgf_comments: "",
    sec_cgf_date: new Date().toLocaleDateString(),
    customer_id: "",
    japc_comments: "",
    assignment_date: new Date().toLocaleDateString(),
    budget_line_id: [],
    //   vendor_id: "",
    certified_amount: "",
    record_status_id: "",
    certification_number: "",
    certification_comments: "",
    cp_date: new Date().toLocaleDateString(),
    treasury_approved: "",
    returned_document_number: "",
    coord_cgf_comments: "",
    coord_cgf_date: new Date().toLocaleDateString(),
    current_management: "",
});

onMounted(() => completeForm(props.certification));

const completeForm = (certification) => {
    if (props.currentOperation !== OPERATIONS.CREATE) {
        form.certification_memo = certification.certification_memo;
        form.content = certification.content;
        form.contract_object = certification.contract_object;
        form.process_type_id = certification.process_type_id;
        form.expense_type_id = certification.expense_type_id;
        form.department_id = certification.department_id;
        form.sec_cgf_comments = certification.sec_cgf_comments;
        form.sec_cgf_date =
            certification.sec_cgf_date ?? new Date().toLocaleDateString();
        form.customer_id = certification.customer_id ?? "";
        form.japc_comments = certification.japc_comments;
        form.assignment_date =
            certification.assignment_date ?? new Date().toLocaleDateString();
        form.budget_line_id = certification.certification_budget_lines.map(
            (item) => item.budget_line_id
        );
        form.certified_amount = certification.certified_amount;
        form.certification_number = certification.certification_number;
        form.record_status_id = certification.record_status_id ?? "";
        form.certification_comments = certification.certification_comments;
        form.cp_date = certification.cp_date ?? new Date().toLocaleDateString();
        form.treasury_approved = certification.treasury_approved;
        form.returned_document_number = certification.returned_document_number;
        form.coord_cgf_comments = certification.coord_cgf_comments;
        form.coord_cgf_date =
            certification.coord_cgf_date ?? new Date().toLocaleDateString();
        form.current_management = certification.current_management;
    }
};

// ---------------------------------------------------------
// DISABLED
// ---------------------------------------------------------

const disabled = {
    global: computed(
        () =>
            // CONDICIONES PARA BLOQUEOS GLOBALES DE INPUTS
            // 1. ESTAR EN OPERATION = SHOW
            props.currentOperation === OPERATIONS.SHOW ||
            // 2. ESTATUS CANCELADO O LIQUIDADO
            (props.certification.record_status_id &&
                props.certification.record_status_id >= STATUSES.CANCELED) ||
            // 3.
            // 3.1. ROL NO SEA ADMIN
            (role.name !== ROLES.ADMIN &&
                // 3.2.1. Y, QUE NO ESTÉ EN EL FORMULARIO QUE LE CORRESPONDE
                (activePhase.value !== role.step ||
                    // 3.2.2. O QUE ESTATUS SEA APROBADO U OTRO
                    (props.certification.record_status_id &&
                        props.certification.record_status_id >=
                            STATUSES.APPROVED)))
    ),
    // CONDICIONES PARA BLOQUEO DE INPUT CERTIFICATION
    // 1. CUANDO NO SEA IGUAL A REGISTRADO
    certification_number: computed(
        () => form.record_status_id !== STATUSES.REGISTERED
    ),
    button: ref(false),
};
// CONDICIONES PARA BLOQUEO DE BOTON DE ENVIAR
disabled.button.value =
    (props.currentOperation !== OPERATIONS.SHOW &&
        role.name !== ROLES.ADMIN &&
        (activePhase.value !== role.step ||
            (props.certification.record_status_id &&
                props.certification.record_status_id >= STATUSES.APPROVED))) ||
    form.processing;

// ---------------------------------------------------------
// CERTIFICATIONS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
        current_management: STEPS.STEP_1,
    })).post(route("certifications.store"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// ---------------------------------------------------------
// CERTIFICATIONS.UPDATE
// ---------------------------------------------------------
const update = () => {
    form.transform((data) => ({
        ...data,
        record_status_id:
            form.record_status_id > STATUSES.REGISTERED
                ? props.certification.record_status_id
                : form.record_status_id,
    })).put(route("certifications.update", props.certification.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const transaction = () => {
    return props.currentOperation === OPERATIONS.CREATE
        ? create()
        : props.currentOperation === OPERATIONS.SHOW
        ? ""
        : props.currentOperation === OPERATIONS.UPDATE
        ? update()
        : "";
};

// SHOW
const getCertification = (id) => {
    axios
        .get(route("certifications.show", id))
        .then((response) => {
            if (response) {
                console.log(response.data);
                completeForm(response.data);
            }
        })
        .catch((error) => {
            console.error(error);
        });
};

// --------------------------------------------
// MANEJO DE FECHAS
// --------------------------------------------
const formattedDate = (date) => {
    return dayjs(date).isValid()
        ? dayjs(date).format("YYYY-MM-DD HH:mm:ss")
        : "-";
};
</script>

<template>
    <FormValidationErrors v-if="form.hasErrors" />

    <Stepper
        v-model="activePhase"
        :steps="roles"
        :operation="currentOperation"
        :current-management="
            currentOperation !== OPERATIONS.CREATE
                ? certification.current_management
                : null
        "
    />

    <CardBox is-form :in-modal="inModal" @submit.prevent="transaction">
        <!-- STEP 1 -->
        <div
            v-show="activePhase === STEPS.STEP_1"
            class="transition duration-500 ease-in-out"
        >
            <FormField
                label="Objeto de contrato"
                label-for="contract_object"
                :errors="form.errors.contract_object"
                required
            >
                <FormControl
                    v-model="form.contract_object"
                    id="contract_object"
                    :icon="mdiCardAccountDetails"
                    autocomplete="contract_object"
                    type="text"
                    placeholder="Detalle el objeto de contrato"
                    :has-errors="form.errors.contract_object != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
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
                        placeholder="Ej: IESS-HTMC-JATSGCME-2022-5073-M"
                        :has-errors="form.errors.certification_memo != null"
                        :disabled="disabled.global.value"
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
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Tipo de proceso"
                    label-for="process_type_id"
                    help="Escoja el tipo de proceso"
                    :errors="form.errors.process_type_id"
                    required
                >
                    <FormControl
                        v-model="form.process_type_id"
                        name="process_type_id"
                        id="process_type_id"
                        :icon="mdiFormatListBulletedType"
                        autocomplete="process_type_id"
                        :options="selectOptions.processType"
                        :has-errors="form.errors.process_type_id != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
                <FormField
                    label="Tipo de gasto"
                    label-for="expense_type_id"
                    help="Escoja el tipo de gasto"
                    :errors="form.errors.expense_type_id"
                    required
                >
                    <FormControl
                        v-model="form.expense_type_id"
                        name="expense_type_id"
                        id="expense_type_id"
                        :icon="mdiFormatListBulletedType"
                        autocomplete="expense_type_id"
                        :options="selectOptions.expenseType"
                        :has-errors="form.errors.expense_type_id != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <FormField
                label="Area requirente"
                label-for="department_id"
                help="Seleccione el área requirente"
                :errors="form.errors.department_id"
                required
            >
                <FormControl
                    v-model="form.department_id"
                    name="department_id"
                    id="department_id"
                    :icon="mdiDomain"
                    autocomplete="department_id"
                    :options="selectOptions.requestingArea"
                    :has-errors="form.errors.department_id != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
            <FormField
                label="Observaciones"
                label-for="sec_cgf_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.sec_cgf_comments"
            >
                <FormControl
                    v-model="form.sec_cgf_comments"
                    type="textarea"
                    id="sec_cgf_comments"
                    :icon="mdiTag"
                    autocomplete="sec_cgf_comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.sec_cgf_comments != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
        </div>
        <!-- STEP 2 -->
        <div
            v-show="activePhase === STEPS.STEP_2"
            class="transition duration-500 ease-in-out"
        >
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
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
                        :disabled="disabled.global.value"
                    />
                </FormField>
                <FormField
                    label="Reasignar a "
                    label-for="customer_id"
                    help="Seleccione el usuario a reasignar la gestión"
                    :errors="form.errors.customer_id"
                    required
                >
                    <FormControl
                        v-model="form.customer_id"
                        name="customer_id"
                        id="customer_id"
                        :icon="mdiDomain"
                        autocomplete="customer_id"
                        :options="selectOptions.users"
                        :has-errors="form.errors.customer_id != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <FormField
                label="Observaciones"
                label-for="japc_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.japc_comments"
            >
                <FormControl
                    v-model="form.japc_comments"
                    type="textarea"
                    id="japc_comments"
                    :icon="mdiTag"
                    autocomplete="japc_comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.japc_comments != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
        </div>
        <!-- STEP 3 -->
        <div
            v-show="activePhase === STEPS.STEP_3"
            class="transition duration-500 ease-in-out"
        >
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Item presupuestario"
                    label-for="budget_line_id"
                    help="Seleccione el código presupuestario"
                    :errors="form.errors.budget_line_id"
                    required
                >
                    <FormControlBudgetLine
                        v-model="form.budget_line_id"
                        name="budget_line_id"
                        id="budget_line_id"
                        :icon="mdiDomain"
                        autocomplete="budget_line_id"
                        :options="selectOptions.budgetLine"
                        :has-errors="form.errors.budget_line_id != null"
                        :disabled="disabled.global.value"
                        multiple
                    />
                </FormField>
                <FormField
                    label="Monto certificado"
                    label-for="certified_amount"
                    help="Ingrese el monto certificado"
                    :errors="form.errors.certified_amount"
                    :required="form.record_status_id === STATUSES.REGISTERED"
                >
                    <FormControl
                        v-model="form.certified_amount"
                        id="certified_amount"
                        :icon="mdiCurrencyUsd"
                        autocomplete="certified_amount"
                        type="number"
                        inputmode="decimal"
                        placeholder="Ej: 1534.35"
                        :step="0.0001"
                        :min="0"
                        :has-errors="form.errors.certified_amount != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Estado de certificación"
                    label-for="record_status_id"
                    help="Seleccione el estado de la certificación"
                    :errors="form.errors.record_status_id"
                    required
                >
                    <FormControl
                        v-model="form.record_status_id"
                        name="record_status_id"
                        id="record_status_id"
                        :icon="mdiDomain"
                        autocomplete="record_status_id"
                        :options="selectOptions.recordStatus"
                        :has-errors="form.errors.record_status_id != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
                <FormField
                    label="Número de Certificación"
                    label-for="certification_number"
                    help="Digite el número de certificación"
                    :errors="form.errors.certification_number"
                    :required="form.record_status_id === STATUSES.REGISTERED"
                >
                    <FormControl
                        v-model="form.certification_number"
                        id="certification_number"
                        :icon="mdiNumeric"
                        autocomplete="certification_number"
                        placeholder="Ej: 123"
                        type="number"
                        :has-errors="form.errors.certification_number != null"
                        :disabled="
                            disabled.global.value ||
                            disabled.certification_number.value
                        "
                    />
                </FormField>
            </div>
            <FormField
                label="Observaciones"
                label-for="certification_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.certification_comments"
            >
                <FormControl
                    v-model="form.certification_comments"
                    type="textarea"
                    id="certification_comments"
                    :icon="mdiTag"
                    autocomplete="certification_comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.certification_comments != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
        </div>
        <!-- STEP 4 -->
        <div
            v-show="activePhase === STEPS.STEP_4"
            class="transition duration-500 ease-in-out"
        >
            <FormField
                label="Revisión de certificación"
                label-for="treasury_approved"
                :errors="form.errors.treasury_approved"
                required
            >
                <FormCheckRadioGroup
                    v-model="form.treasury_approved"
                    name="treasury_approved"
                    type="radio"
                    :options="{
                        approved: 'Aprobado',
                        returned: 'Devuelto',
                        canceled: 'Anulado',
                        liquidated: 'Liquidado',
                    }"
                    :disabled="disabled.global.value"
                />
            </FormField>
            <FormField
                label="Nro. Memorando"
                label-for="returned_document_number"
                help="Ingrese el Nro. de Memorando de la revisión de Certificación"
                :errors="form.errors.returned_document_number"
                required
            >
                <FormControl
                    v-model="form.returned_document_number"
                    id="returned_document_number"
                    :icon="mdiNumeric"
                    autocomplete="returned_document_number"
                    type="text"
                    placeholder="Ej: IESS-HTMC-JATSGCME-2022-5073-M"
                    :has-errors="form.errors.returned_document_number != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
            <FormField
                label="Observaciones"
                label-for="coord_cgf_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.coord_cgf_comments"
            >
                <FormControl
                    v-model="form.coord_cgf_comments"
                    type="textarea"
                    id="coord_cgf_comments"
                    :icon="mdiTag"
                    autocomplete="coord_cgf_comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.coord_cgf_comments != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
        </div>
        <BaseDivider v-show="!inModal" />
        <BaseLevel>
            <BaseButtons type="justify-start" v-show="withButton">
                <BaseButton
                    type="submit"
                    color="success"
                    :label="elementProps.label"
                    :class="{
                        'opacity-25': disabled.button.value,
                    }"
                    :disabled="disabled.button.value"
                    :icon="
                        currentOperation === OPERATIONS.SHOW
                            ? mdiPrinter
                            : mdiContentSaveAll
                    "
                />
                <BaseButton
                    route-name="certifications.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </BaseButtons>
            <LabelDate
                v-if="currentOperation !== OPERATIONS.CREATE"
                :date-one="formattedDate(certification.sec_cgf_date)"
                :date-two="formattedDate(certification.assignment_date)"
                :date-three="formattedDate(certification.cp_date)"
                :date-four="formattedDate(certification.coord_cgf_date)"
                v-model:activePhase="activePhase"
            />
        </BaseLevel>
    </CardBox>
</template>
