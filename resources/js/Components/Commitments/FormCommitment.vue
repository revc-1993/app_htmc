<script setup>
import { computed, ref } from "vue";
import axios from "axios";
import { router, useForm, usePage } from "@inertiajs/vue3";
import {
    mdiFormatListBulletedType,
    mdiTag,
    mdiDomain,
    mdiNumeric,
    mdiCurrencyUsd,
    mdiBriefcasePlus,
    mdiMagnify,
    mdiCardAccountDetails,
    mdiContentSaveAll,
    mdiBackspace,
} from "@mdi/js";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import Stepper from "@/components/Stepper.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import CardBoxModalVendor from "@/components/vendors/CardBoxModalVendor.vue";
import FormValidationErrors from "@/components/FormValidationErrors.vue";
import BaseButtons from "../BaseButtons.vue";
import Toast from "@/components/Toast.vue";

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
});

// ---------------------------------------------------------
// CONSTANTES
// ---------------------------------------------------------
const operations = {
    create: 1,
    update: 3,
};

const statuses = {
    pendingReview: 1,
    reviewing: 2,
    observed: 3,
    registered: 4,
    returned: 5,
    approved: 6,
    liquidated: 7,
};

const role = computed(() => usePage().props.auth.user.roles[0].id);
const activePhase = ref(1);
activePhase.value = role.value ?? 1;

const contractObject = ref("");
const errorSearch = ref(false);

const steps = [
    {
        id: 2,
        key: 1,
        label: "Secretaría JAPC",
    },
    {
        id: 3,
        key: 2,
        label: "Analista de Certificación",
    },
    {
        id: 4,
        key: 3,
        label: "Coordinación General Financiera",
    },
];

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

let selectOptions = {
    users: optionSelect(props.users),
    recordStatus: optionSelect(props.recordStatuses),
};

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm(
    props.currentOperation === operations.create
        ? {
              commitment_memo: "",
              process_number: "",
              contract_administrator: "",
              assignment_date: new Date().toLocaleDateString(),
              japc_comments: "",
              customer_id: "",

              commitment_cur: "",
              commitment_amount: "",
              commitment_comments: "",
              commitment_date: new Date().toLocaleDateString(),
              vendor_id: "",

              treasury_approved: "",
              returned_document_number: "",
              coord_cgf_comments: "",
              coord_cgf_date: new Date().toLocaleDateString(),
          }
        : {
              commitment_memo: props.commitment.commitment_memo,
              process_number: props.commitment.process_number,
              contract_administrator: props.commitment.contract_administrator,
              assignment_date:
                  props.commitment.assignment_date ??
                  new Date().toLocaleDateString(),
              japc_comments: props.commitment.japc_comments,
              customer_id: props.commitment.customer_id ?? "",

              commitment_cur: props.commitment.commitment_cur,
              commitment_amount: props.commitment.commitment_amount,
              commitment_comments: props.commitment.commitment_comments,
              commitment_date:
                  props.commitment.commitment_date ??
                  new Date().toLocaleDateString(),
              vendor_id: props.commitment.vendor_id,

              treasury_approved: props.commitment.treasury_approved,
              returned_document_number:
                  props.commitment.returned_document_number,
              coord_cgf_comments: props.commitment.coord_cgf_comments,
              coord_cgf_date:
                  props.commitment.coord_cgf_date ??
                  new Date().toLocaleDateString(),
          }
);

const formSearchVendor = useForm({
    certification_number: "",
});

// ---------------------------------------------------------
// DISABLED
// ---------------------------------------------------------
const disabled = {
    global: computed(
        () =>
            props.currentOperation === operations.show ||
            activePhase.value !== role.value ||
            (props.commitment.record_status_id &&
                props.commitment.record_status_id === statuses.liquidated &&
                role.value < 4)
    ),
    expense_type: computed(
        () =>
            typeof props.commitment.expense_type_id &&
            props.commitment.expense_type_id === 1
    ),
    record_status: computed(
        () =>
            props.commitment.record_status_id &&
            props.commitment.record_status_id >= statuses.approved
    ),
    commitment_cur: computed(
        () => form.record_status_id !== statuses.registered
    ),
};

const disabledButton = ref(false);
disabledButton.value =
    (role.value <= 3 &&
        props.commitment.record_status_id &&
        props.commitment.record_status_id >= statuses.approved) ||
    form.processing;

// ---------------------------------------------------------
// COMMITMENTS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
    })).post(route("commitments.store"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// ---------------------------------------------------------
// COMMITMENTS.UPDATE
// ---------------------------------------------------------
const update = () => {
    form.transform((data) => ({
        ...data,
    })).put(route("commitments.update", props.commitments.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// --------------------------------------------
// COMMITMENTS.DELETE
// --------------------------------------------
const destroy = () => {
    router.delete(`/commitments/${props.commitments.id}`, {
        onSuccess: () => confirm(),
    });
};

const transaction = () => {
    return props.currentOperation === operations.create
        ? create()
        : props.currentOperation === operations.update
        ? update()
        : "";
};

// --------------------------------------------
// PROVEEDOR
// --------------------------------------------
const messageOperation = ref(null);

const toast = ref(false);
const messageVendor = ref("");

const searchVendorByNit = (alert = "") => {
    axios
        .get("/commitments/getVendorByNit?nit=" + formSearchVendor.nit)
        .then((response) => {
            if (response) {
                router.reload({ only: ["FormControl"] });
                form.vendor_id = response.data.vendor.id;
                formSearchVendor.nit = response.data.vendor.nit;
                vendor.value = response.data.vendor.name;

                if (alert === "alert") {
                    toast.value = true;
                    messageVendor.value = {
                        response:
                            "Se encontró el proveedor con NIT " +
                            formSearchVendor.nit,
                        operation: 1,
                    };
                }
            }
        })
        .catch((error) => {
            errorSearch.value = true;
            router.reload({ only: ["FormControl"] });
            form.vendor_id = null;
            vendor.value = "";
            console.log(error);

            if (alert === "alert") {
                toast.value = true;
                messageVendor.value = {
                    response: "No se encontró el NIT digitado",
                    operation: 4,
                };
            }
        });
};

const searchVendorById = () => {
    if (form.vendor_id) {
        axios
            .get("/commitments/getVendorById?id=" + props.commitment.vendor_id)
            .then((response) => {
                if (response) {
                    formSearchVendor.nit = response.data.vendor.nit;
                    vendor.value = response.data.vendor.name;
                }
            })
            .catch((error) => {
                form.vendor_id = null;
                vendor.value = "";
                console.log(error);
            });
    }
};

searchVendorById();

// --------------------------------------------
// MODAL DE CREAR PROVEEDOR: 1 Create, 2 Show, 3 Update, 4 Delete
// --------------------------------------------
// const isModalActive = ref(false);
// const currentOperationVendor = ref(1);
// const newVendor = ref({});

// const openModal = () => {
//     isModalActive.value = true;
// };

// const closeModal = (event) => {
//     if (event === "confirm") {
//         formSearchVendor.nit = newVendor.value.nit;
//         searchVendorByNit();
//         toast.value = true;
//         messageVendor.value = {
//             response: "Proveedor creado exitosamente.",
//             operation: 1,
//         };
//     }
//     isModalActive.value = false;
// };
</script>

<template>
    <!-- <CardBoxModalVendor
        v-model="isModalActive"
        v-model:vendor="newVendor"
        :current-operation="1"
        @confirm="closeModal"
    /> -->

    <Toast v-if="toast" v-model="toast" :message="messageVendor" />

    <FormValidationErrors v-if="form.hasErrors" />

    <Stepper
        v-model="activePhase"
        :steps="steps"
        :operation="currentOperation"
        :current-management="
            currentOperation !== operations.create
                ? commitment.current_management
                : null
        "
    />

    <CardBox is-form @submit.prevent="transaction">
        <!-- STEP 1 -->
        <div
            v-show="activePhase === 1"
            class="transition duration-500 ease-in-out"
        >
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de asignación: </strong>
                {{ form.assignment_date }}
            </div>

            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <BaseLevel>
                    <FormField
                        label="Número de Certificación"
                        label-for="certification_number"
                        :errors="form.errors.certification_number"
                    >
                        <form @submit.prevent="searchVendorByNit('alert')">
                            <FormControl
                                v-model="formSearchVendor.certification_number"
                                id="certification_number"
                                name="certification_number"
                                :icon="mdiCardAccountDetails"
                                autocomplete="certification_number"
                                type="text"
                                placeholder="Digite el número de certificación"
                                :has-errors="
                                    form.errors.certification_number != null ||
                                    errorSearch
                                "
                                :disabled="
                                    disabled.global.value ||
                                    disabled.expense_type.value
                                "
                            >
                            </FormControl>
                            <BaseButtons>
                                <BaseButton
                                    type="submit"
                                    color="info"
                                    :icon="mdiMagnify"
                                    tooltip="Buscar"
                                    small
                                    :disabled="
                                        disabled.expense_type.value ||
                                        disabled.global.value ||
                                        disabledButton
                                    "
                                />
                                <!-- <BaseButton
                                    color="success"
                                    :icon="mdiBriefcasePlus"
                                    tooltip="Nuevo proveedor"
                                    small
                                    :disabled="
                                        disabled.expense_type.value ||
                                        disabled.global.value ||
                                        disabledButton
                                    "
                                    @click="openModal"
                                /> -->
                            </BaseButtons>
                        </form>
                    </FormField>
                </BaseLevel>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Nro. Memorando de compromiso"
                    label-for="commitment_memo"
                    help="Ingrese el Nro. de Memorando del compromiso"
                    :errors="form.errors.commitment_memo"
                >
                    <FormControl
                        v-model="form.commitment_memo"
                        id="commitment_memo"
                        :icon="mdiNumeric"
                        autocomplete="commitment_memo"
                        type="text"
                        placeholder="Ej: IESS-HTMC-JATSGCME-2022-5073-M"
                        :has-errors="form.errors.commitment_memo != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
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
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Administrador de contrato"
                    label-for="contract_administrator"
                    :errors="form.errors.contract_administrator"
                >
                    <FormControl
                        v-model="form.contract_administrator"
                        id="contract_administrator"
                        :icon="mdiCardAccountDetails"
                        autocomplete="contract_administrator"
                        type="text"
                        placeholder="Detalle los nombres del administrador de contrato"
                        :has-errors="form.errors.contract_administrator != null"
                        :disabled="disabled.global.value"
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
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
        </div>
        <!-- STEP 2 -->
        <!-- <div
            v-show="activePhase === 2"
            class="transition duration-500 ease-in-out"
        >
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de asignación JAPC: </strong>
                {{ form.assignment_date }}
            </div>

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
        </div> -->
        <!-- STEP 3 -->
        <!-- <div
            v-show="activePhase === 3"
            class="transition duration-500 ease-in-out"
        >
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de certificación: </strong>
                {{ form.cp_date }}
            </div>

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
                    :disabled="disabled.global.value"
                />
            </FormField>

            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
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
                        :disabled="
                            disabled.global.value || disabled.expense_type.value
                        "
                    />
                </FormField>
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
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="NIT de Proveedor"
                    label-for="nit"
                    :errors="form.errors.vendor_id"
                >
                    <form @submit.prevent="searchVendorByNit('alert')">
                        <BaseLevel>
                            <FormControl
                                v-model="formSearchVendor.nit"
                                id="nit"
                                name="nit"
                                :icon="mdiCardAccountDetails"
                                autocomplete="nit"
                                type="text"
                                placeholder="Detalle el NIT del Proveedor"
                                :has-errors="
                                    form.errors.vendor_id != null || errorSearch
                                "
                                :disabled="
                                    disabled.global.value ||
                                    disabled.expense_type.value
                                "
                            >
                            </FormControl>
                            <BaseButtons>
                                <BaseButton
                                    type="submit"
                                    color="info"
                                    :icon="mdiMagnify"
                                    tooltip="Buscar"
                                    small
                                    :disabled="
                                        disabled.expense_type.value ||
                                        disabled.global.value ||
                                        disabledButton
                                    "
                                />
                                <BaseButton
                                    color="success"
                                    :icon="mdiBriefcasePlus"
                                    tooltip="Nuevo proveedor"
                                    small
                                    :disabled="
                                        disabled.expense_type.value ||
                                        disabled.global.value ||
                                        disabledButton
                                    "
                                    @click="openModal"
                                />
                            </BaseButtons>
                        </BaseLevel>
                    </form>
                </FormField>
                <FormField label="Nombre de proveedor" label-for="name">
                    <FormControl
                        v-model="vendor"
                        id="name"
                        name="name"
                        :icon="mdiCardAccountDetails"
                        placeholder="Nombre de proveedor"
                        autocomplete="name"
                        type="text"
                        disabled
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
                >
                    <FormControl
                        v-model="form.record_status_id"
                        name="record_status_id"
                        id="record_status_id"
                        :icon="mdiDomain"
                        autocomplete="record_status_id"
                        :options="selectOptions.recordStatus"
                        :has-errors="form.errors.record_status_id != null"
                        :disabled="
                            disabled.global.value ||
                            disabled.record_status.value
                        "
                    />
                </FormField>
                <FormField
                    label="Número de Certificación"
                    label-for="certification_number"
                    help="Digite el número de certificación"
                    :errors="form.errors.certification_number"
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
        </div> -->
        <!-- STEP 4 -->
        <div
            v-show="activePhase === 4"
            class="transition duration-500 ease-in-out"
        >
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de revisión: </strong>
                {{ form.coord_cgf_date }}
            </div>

            <FormField label="Revisión de certificación">
                <FormCheckRadioGroup
                    v-model="form.treasury_approved"
                    name="treasury_approved"
                    type="radio"
                    :options="{
                        approved: 'Aprobado',
                        returned: 'Devuelto',
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
        <BaseDivider />
        <BaseButtons>
            <BaseButton
                type="submit"
                color="success"
                :label="elementProps.label"
                :class="{ 'opacity-25': disabledButton }"
                :disabled="disabledButton"
                :icon="mdiContentSaveAll"
            />
            <BaseButton
                route-name="commitments.index"
                color="lightDark"
                label="Regresar"
                :icon="mdiBackspace"
            />
        </BaseButtons>
    </CardBox>
</template>
