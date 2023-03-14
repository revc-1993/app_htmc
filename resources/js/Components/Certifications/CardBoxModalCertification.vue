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
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    certification: Object,
    departments: Object,
    processTypes: Object,
    expenseTypes: Object,
    budgetLines: Object,
    users: Object,
    recordStatuses: Object,
    currentOperation: {
        type: [String, Number, Boolean],
        default: null,
    },
    elementProps: Object,
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
    destroy: 4,
};

const role = computed(() => usePage().props.auth.user.roles[0].id);
const activePhase = ref(1);
activePhase.value = role.value ?? 1;

const disabled = computed(
    () =>
        props.currentOperation === operations.show ||
        activePhase.value !== role.value
);

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

let selectOptions = {
    requestingArea: optionSelect(props.departments),
    processType: optionSelect(props.processTypes),
    expenseType: optionSelect(props.expenseTypes),
    budgetLine: optionSelect(props.budgetLines),
    users: optionSelect(props.users),
    recordStatus: optionSelect(props.recordStatuses),
};

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm(
    props.currentOperation === operations.create
        ? {
              certification_memo: "",
              content: "",
              contract_object: "",
              process_type_id: "",
              expense_type_id: "",
              department_id: "",
              cgf_comments: "",
              sec_cgf_date: new Date().toLocaleDateString(),
              customer_id: "",
              japc_comments: "",
              japc_date: new Date().toLocaleDateString(),
              process_number: "",
              budget_line_id: "",
              vendor_id: "",
              certified_amount: "",
              record_status: "",
              certification_comments: "",
              cp_date: new Date().toLocaleDateString(),
              treasury_approved: "",
              returned_document_number: "",
          }
        : {
              certification_memo: props.certification.certification_memo,
              content: props.certification.content,
              contract_object: props.certification.contract_object,
              process_type_id: props.certification.process_type_id,
              expense_type_id: props.certification.expense_type_id,
              department_id: props.certification.department_id,
              cgf_comments: props.certification.cgf_comments,
              sec_cgf_date: props.certification.sec_cgf_date,
              customer_id: props.certification.customer_id ?? "",
              japc_comments: props.certification.japc_comments,
              japc_date: props.certification.japc_date,
              process_number: props.certification.process_number,
              budget_line_id: props.certification.budget_line_id,
              vendor_id: props.certification.vendor_id,
              certified_amount: props.certification.certified_amount,
              record_status: props.certification.record_status
                  ? props.certification.record_status.id
                  : "",
              certification_comments:
                  props.certification.certification_comments,
              cp_date: props.certification.cp_date,
              treasury_approved: props.certification.treasury_approved,
              returned_document_number:
                  props.certification.returned_document_number,
          }
);

const formSearch = useForm({ vendor_nit: "" });

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
        // onStart: () => console.log("UPDATE"),
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

const transaction = () => {
    return props.currentOperation === operations.create
        ? create()
        : props.currentOperation === operations.update
        ? update()
        : props.currentOperation === operations.delete
        ? destroy()
        : "";
};

const search = () => {
    router.get(`/vendors/${formSearch.vendor_nit}`);
};
</script>

<template>
    <CardBoxModal
        v-model="value"
        :title="elementProps.label"
        :button="elementProps.color"
        :button-label="elementProps.label"
        has-cancel
        is-form
        @confirm="transaction"
    >
        <template v-if="currentOperation !== operations.delete">
            <Stepper
                v-model="activePhase"
                :operation="currentOperation"
                :current_management="
                    currentOperation !== operations.create
                        ? certification.current_management
                        : null
                "
            />

            <!-- STEP 1 -->
            <div
                v-show="activePhase === 1"
                class="transition duration-500 ease-in-out"
            >
                <FormField
                    label="Fecha de ingreso"
                    label-for="sec_cgf_date"
                    help="Fecha de ingreso de secretaría CGF"
                    :errors="form.errors.sec_cgf_date"
                >
                    <FormControl
                        v-model="form.sec_cgf_date"
                        id="sec_cgf_date"
                        :icon="mdiCalendarRange"
                        autocomplete="sec_cgf_date"
                        type="date"
                        :has-errors="form.errors.sec_cgf_date != null"
                        disabled
                    />
                </FormField>
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
                <div class="grid grid-cols-1 gap-x-3 lg:grid-cols-2">
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
                </div>
                <div class="grid grid-cols-1 gap-x-3 lg:grid-cols-2">
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
                </div>
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
            <div
                v-show="activePhase === 2"
                class="transition duration-500 ease-in-out"
            >
                <FormField
                    label="Fecha de asignación JAPC"
                    label-for="japc_date"
                    help="Fecha de asignación de secretaría JAPC"
                    :errors="form.errors.japc_date"
                >
                    <FormControl
                        v-model="form.japc_date"
                        id="japc_date"
                        :icon="mdiCalendarRange"
                        autocomplete="japc_date"
                        type="date"
                        :has-errors="form.errors.japc_date != null"
                        disabled
                    />
                </FormField>
                <div class="grid grid-cols-1 gap-x-3 lg:grid-cols-2">
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
                        :disabled="disabled"
                    />
                </FormField>
            </div>
            <!-- STEP 3 -->
            <div
                v-show="activePhase === 3"
                class="transition duration-500 ease-in-out"
            >
                <FormField
                    label="Fecha de certificación"
                    label-for="certified_date"
                    help="Fecha del actualización de la certificación"
                    :errors="form.errors.certified_date"
                >
                    <FormControl
                        v-model="form.certified_date"
                        id="certified_date"
                        :icon="mdiCalendarRange"
                        autocomplete="certified_date"
                        type="date"
                        :has-errors="form.errors.certified_date != null"
                        disabled
                    />
                </FormField>

                <div class="grid grid-cols-1 gap-x-3 lg:grid-cols-2">
                    <FormField
                        label="Nro. Proceso"
                        label-for="process_number"
                        help="Ingrese el Nro. de Proceso"
                        :errors="form.errors.process_number"
                    >
                        <FormControl
                            v-model="form.process_number"
                            id="process_number"
                            :icon="mdiNumeric"
                            autocomplete="process_number"
                            type="text"
                            placeholder="Ej: CE-2023000384849"
                            :has-errors="form.errors.process_number != null"
                            :disabled="disabled"
                        />
                    </FormField>
                    <FormField
                        label="Código presupuestario"
                        label-for="budget_line_id"
                        help="Seleccione el código presupuestario"
                        :errors="form.errors.budget_line_id"
                    >
                        <FormControl
                            v-model="form.budget_line_id"
                            name="budget_line_id"
                            id="budget_line_id"
                            :icon="mdiDomain"
                            autocomplete="budget_line_id"
                            :options="selectOptions.budgetLine"
                            :has-errors="form.errors.budget_line_id != null"
                            :disabled="disabled"
                        />
                    </FormField>
                </div>
                <FormField
                    label="NIT de Proveedor"
                    label-for="vendor_nit"
                    :errors="formSearch.errors.vendor_nit"
                >
                    <BaseButtons>
                        <form>
                            <FormControl
                                v-model="formSearch.vendor_nit"
                                id="vendor_nit"
                                :icon="mdiCardAccountDetails"
                                autocomplete="vendor_nit"
                                type="text"
                                placeholder="Detalle el NIT del Proveedor"
                                :has-errors="
                                    formSearch.errors.vendor_nit != null
                                "
                                :disabled="disabled"
                            />
                            <BaseButton
                                color="info"
                                icon="mdiMagnify"
                                tooltip="Buscar"
                                small
                                @click.prevent="search"
                            />
                        </form>
                    </BaseButtons>
                </FormField>
                <div class="grid grid-cols-1 gap-x-3 lg:grid-cols-2">
                    <FormField
                        label="Monto certificado"
                        label-for="certified_amount"
                        help="Ingrese el monto certificado"
                        :errors="form.errors.certified_amount"
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
                            :disabled="disabled"
                        />
                    </FormField>
                    <FormField
                        label="Estado de certificación"
                        label-for="record_status"
                        help="Seleccione el estado de la certificación"
                        :errors="form.errors.record_status"
                    >
                        <FormControl
                            v-model="form.record_status"
                            name="record_status"
                            id="record_status"
                            :icon="mdiDomain"
                            autocomplete="record_status"
                            :options="selectOptions.recordStatus"
                            :has-errors="form.errors.record_status != null"
                            :disabled="disabled"
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
                        :disabled="disabled"
                    />
                </FormField>
            </div>
            <!-- STEP 4 -->
            <div
                v-show="activePhase === 4"
                class="transition duration-500 ease-in-out"
            >
                <FormField label="Revisión de certificación">
                    <FormCheckRadioGroup
                        v-model="form.treasury_approved"
                        name="treasury_approved"
                        type="radio"
                        :options="{ true: 'Aprobado', false: 'No aprobado' }"
                        :disabled="disabled"
                    />
                </FormField>
                <FormField
                    label="Nro. Memorando de Devuelto"
                    label-for="returned_document_number"
                    help="Ingrese el Nro. de Memorando de la devolución de Certificación"
                    :errors="form.errors.returned_document_number"
                >
                    <FormControl
                        v-model="form.returned_document_number"
                        id="returned_document_number"
                        :icon="mdiNumeric"
                        autocomplete="returned_document_number"
                        type="text"
                        placeholder="Ej: HTMC-JATSGCME-2022-5073-M"
                        pattern="/^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/gm"
                        :has-errors="
                            form.errors.returned_document_number != null
                        "
                        :disabled="
                            disabled || form.treasury_approved != 'false'
                        "
                    />
                </FormField>
            </div>
        </template>
        <template v-else>
            <strong>¿Está seguro de continuar con esta acción?</strong>
            <p>No se podrá recuperar recuperar los datos eliminados.</p>
        </template>
    </CardBoxModal>
</template>
