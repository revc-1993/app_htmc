<script setup>
import { computed, ref, onMounted } from "vue";
import axios from "axios";
import { router, useForm, usePage } from "@inertiajs/vue3";
import {
    mdiTag,
    mdiDomain,
    mdiNumeric,
    mdiCurrencyUsd,
    mdiMagnify,
    mdiCardAccountDetails,
    mdiContentSaveAll,
    mdiBackspace,
    mdiPrinter,
    mdiBriefcasePlus,
} from "@mdi/js";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import FormControlWithButton from "@/Components/FormControlWithButton.vue";
import Stepper from "@/Components/Stepper.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import FormValidationErrors from "@/Components/FormValidationErrors.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import Toast from "@/Components/Toast.vue";
import LabelDate from "@/Components/LabelDate.vue";

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
    accrual: {
        type: Object,
        default: {},
    },
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
});

// ---------------------------------------------------------
// CONSTANTES
// ---------------------------------------------------------
const role = usePage().props.auth.user.roles;
const activePhase = ref(STEPS.STEP_1);

const toast = ref(false);
const messageResource = ref("");

// ---------------------------------------------------------
// SETS
// ---------------------------------------------------------
activePhase.value =
    role.name !== ROLES.ADMIN
        ? role.step
        : props.accrual &&
          props.accrual.current_management &&
          props.currentOperation === OPERATIONS.SHOW
        ? props.accrual.current_management.step
        : props.accrual &&
          props.accrual.current_management &&
          props.currentOperation === OPERATIONS.UPDATE
        ? props.accrual.current_management
        : STEPS.STEP_1;

const commitment = {
    contract_administrator: ref(""),
    vendor_name: ref(""),
    process_number: ref(""),
    commitment_cur: ref(""),
    balance: ref(0),
};

if (
    (props.currentOperation === OPERATIONS.SHOW ||
        props.currentOperation === OPERATIONS.UPDATE) &&
    props.accrual.commitment
) {
    commitment.process_number.value = props.accrual.commitment?.process_number;
    commitment.contract_administrator.value =
        props.accrual.commitment.contract_administrator?.names;
    commitment.vendor_name.value = props.accrual.commitment.vendor?.name;
    commitment.balance.value = props.accrual.commitment?.balance;
}

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
    users: optionSelect(props.users),
    recordStatus: optionSelect(props.recordStatuses),
};

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm(
    props.currentOperation === OPERATIONS.CREATE
        ? {
              accrual_memo: "",
              voucher_number: "",
              sec_cgf_date: new Date().toLocaleDateString(),
              assignment_date: new Date().toLocaleDateString(),
              japc_comments: "",
              customer_id: "",

              commitment_id: "",
              accrual_cur: "",
              accrual_date: new Date().toLocaleDateString(),
              record_status_id: "",
              accrual_amount: "",
              accrual_comments: "",

              treasury_approved: "",
              returned_document_number: "",
              coord_cgf_comments: "",
              coord_cgf_date: new Date().toLocaleDateString(),

              current_management: "",
          }
        : {
              accrual_memo: props.accrual.accrual_memo,
              sec_cgf_date: props.accrual.sec_cgf_date,
              voucher_number: props.accrual.voucher_number,
              assignment_date: props.accrual.assignment_date,
              japc_comments: props.accrual.japc_comments,
              customer_id: props.accrual.customer_id,

              commitment_id: props.accrual.commitment_id,
              accrual_cur: props.accrual.accrual_cur,
              accrual_date: props.accrual.accrual_date,
              record_status_id: props.accrual.record_status_id,
              accrual_amount: props.accrual.accrual_amount,
              accrual_comments: props.accrual.accrual_comments,

              treasury_approved: props.accrual.treasury_approved,
              returned_document_number: props.accrual.returned_document_number,
              coord_cgf_comments: props.accrual.coord_cgf_comments,
              coord_cgf_date: props.accrual.coord_cgf_date,

              current_management: props.accrual.current_management,
          }
);

const formSearchCommitment = useForm(
    props.currentOperation === OPERATIONS.CREATE
        ? {
              commitmentNumber: "",
          }
        : {
              commitmentNumber: props.accrual.commitment?.commitment_cur,
          }
);

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
            (props.accrual.record_status_id &&
                props.accrual.record_status_id >= STATUSES.CANCELED) ||
            // 3.
            // 3.1. ROL NO SEA ADMIN
            (role.name !== ROLES.ADMIN &&
                // 3.2.1. Y, QUE NO ESTÉ EN EL FORMULARIO QUE LE CORRESPONDE
                (activePhase.value !== role.step ||
                    // 3.2.2. O QUE ESTATUS SEA APROBADO U OTRO
                    (props.accrual.record_status_id &&
                        (props.accrual.record_status_id === STATUSES.CANCELED ||
                            props.accrual.record_status_id ===
                                STATUSES.LIQUIDATED ||
                            (props.accrual.record_status_id ===
                                STATUSES.APPROVED &&
                                role.name !== ROLES.COORD)))))
    ),
    // CONDICIONES PARA BLOQUEO DE INPUT CERTIFICATION
    // 1. CUANDO NO SEA IGUAL A REGISTRADO
    accrual_cur: computed(() => form.record_status_id !== STATUSES.REGISTERED),
    button: ref(false),
};
// CONDICIONES PARA BLOQUEO DE BOTON DE ENVIAR
disabled.button.value =
    (props.currentOperation !== OPERATIONS.SHOW &&
        role.name !== ROLES.ADMIN &&
        (activePhase.value !== role.step ||
            (props.accrual.record_status_id &&
                props.accrual.record_status_id >= STATUSES.APPROVED))) ||
    form.processing ||
    formSearchCommitment.processing;

// ---------------------------------------------------------
// COMMITMENTS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
        current_management: STEPS.STEP_1,
    })).post(route("accruals.store"), {
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
        record_status_id:
            form.record_status_id > STATUSES.REGISTERED
                ? props.accrual.record_status_id
                : form.record_status_id,
    })).put(route("accruals.update", props.accrual.id), {
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

// --------------------------------------------
// BUSCAR CERTIFICACION
// --------------------------------------------
const searchCommitmentByNumber = (alert = "") => {
    axios
        .get(
            "/commitments/getCommitmentByNumber?commitment_number=" +
                formSearchCommitment.commitmentNumber
        )
        .then((response) => {
            if (response) {
                form.clearErrors("commitment_id");
                router.reload({ only: ["FormControl"] });
                form.commitment_id = response.data.commitment.id;
                commitment.balance.value = response.data.commitment.balance;
                commitment.contract_administrator.value =
                    response.data.commitment.contract_administrator.names;
                commitment.process_number.value =
                    response.data.commitment.process_number;
                commitment.vendor_name.value =
                    response.data.commitment.vendor.name;
                if (alert === "alert") {
                    toast.value = true;
                    messageResource.value = {
                        response:
                            "Se encontró el Compromiso Nro. " +
                            formSearchCommitment.commitmentNumber,
                        operation: 1,
                    };
                }
            }
        })
        .catch((error) => {
            form.setError(
                "commitment_id",
                "No se encontró este Nro. de Compromiso."
            );
            router.reload({ only: ["FormControl"] });
            if (form.commitment_id !== null) form.commitment_id = null;
            commitment.balance.value = "";
            commitment.contract_administrator.value = "";
            commitment.process_number.value = "";
            commitment.vendor_name.value = "";
            console.log(error);
        });
};

// --------------------------------------------
// MANEJO DE FECHAS
// --------------------------------------------
const formattedDate = (date) => {
    return dayjs(date).format("YYYY-MM-DD HH:mm:ss");
};
</script>

<template>
    <Toast v-if="toast" v-model="toast" :message="messageResource" />
    <FormValidationErrors v-if="form.hasErrors" />

    <Stepper
        v-model="activePhase"
        :steps="roles"
        :operation="currentOperation"
        :current-management="
            currentOperation !== OPERATIONS.CREATE
                ? accrual.current_management
                : null
        "
    />

    <CardBox is-form :in-modal="inModal" @submit.prevent="transaction">
        <!-- STEP 1 -->
        <div
            v-show="activePhase === STEPS.STEP_1"
            class="transition duration-500 ease-in-out"
        >
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Nro. Memorando de devengado"
                    label-for="accrual_memo"
                    help="Ingrese el Nro. de Memorando del devengado"
                    :errors="form.errors.accrual_memo"
                    required
                >
                    <FormControl
                        v-model="form.accrual_memo"
                        id="accrual_memo"
                        :icon="mdiNumeric"
                        autocomplete="accrual_memo"
                        type="text"
                        placeholder="Ej: IESS-HTMC-JATSGCME-2022-5073-M"
                        :has-errors="form.errors.accrual_memo != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
                <FormField
                    label="Nro. del comprobante"
                    label-for="voucher_number"
                    help="Ingrese el Nro. de Comprobante"
                    :errors="form.errors.accrual_memo"
                >
                    <FormControl
                        v-model="form.voucher_number"
                        id="voucher_number"
                        :icon="mdiNumeric"
                        autocomplete="voucher_number"
                        type="text"
                        placeholder="Ej: FACT-001-002-000003948"
                        :has-errors="form.errors.voucher_number != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <FormField
                label="Observaciones"
                label-for="sec_cgf_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.japc_comments"
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
                    label="Nro. Memorando de devengado"
                    label-for="accrual_memo"
                    help="Ingrese el Nro. de Memorando del devengado"
                    :errors="form.errors.accrual_memo"
                >
                    <FormControl
                        v-model="form.accrual_memo"
                        id="accrual_memo"
                        :icon="mdiNumeric"
                        autocomplete="accrual_memo"
                        type="text"
                        placeholder="Ej: IESS-HTMC-JATSGCME-2022-5073-M"
                        :has-errors="form.errors.accrual_memo != null"
                        disabled
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
        <div
            v-show="activePhase === STEPS.STEP_3"
            class="transition duration-500 ease-in-out"
        >
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Número de Compromiso"
                    label-for="commitmentNumber"
                    :errors="form.errors.commitment_id"
                    help="Digite el número de compromiso"
                    required
                >
                    <form @submit.prevent="searchCommitmentByNumber('alert')">
                        <FormControlWithButton
                            v-model="formSearchCommitment.commitmentNumber"
                            id="commitmentNumber"
                            name="commitmentNumber"
                            :icon="mdiCardAccountDetails"
                            autocomplete="commitmentNumber"
                            type="text"
                            placeholder="Digite el número de compromiso"
                            :has-errors="form.errors.commitment_id != null"
                            :disabled="disabled.global.value"
                        >
                            <BaseButton
                                type="submit"
                                color="info"
                                :disabled="disabled.global.value"
                                :icon="mdiMagnify"
                                tooltip="Buscar"
                                location="end"
                            />
                        </FormControlWithButton>
                    </form>
                </FormField>
                <FormField label="Nro. de proceso" label-for="process_number">
                    <FormControl
                        v-model="commitment.process_number.value"
                        id="process_number"
                        name="process_number"
                        :icon="mdiCardAccountDetails"
                        placeholder="Nro. de proceso"
                        autocomplete="process_number"
                        type="text"
                        disabled
                    />
                </FormField>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Administrador de contrato"
                    label-for="contract_administrator"
                >
                    <FormControl
                        v-model="commitment.contract_administrator.value"
                        id="contract_administrator"
                        name="contract_administrator"
                        :icon="mdiCardAccountDetails"
                        placeholder="Administrador de contrato"
                        autocomplete="contract_administrator"
                        type="text"
                        disabled
                    />
                </FormField>
                <FormField label="Proveedor" label-for="vendor_name">
                    <FormControl
                        v-model="commitment.vendor_name.value"
                        id="vendor_name"
                        :icon="mdiCurrencyUsd"
                        autocomplete="vendor_name"
                        type="text"
                        placeholder="Proveedor"
                        disabled
                    />
                </FormField>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Saldo del compromiso"
                    label-for="commitment_balance"
                    help="Saldo del compromiso seleccionado"
                >
                    <FormControl
                        v-model="commitment.balance.value"
                        name="commitment_balance"
                        id="commitment_balance"
                        :icon="mdiCurrencyUsd"
                        autocomplete="commitment_balance"
                        disabled
                    />
                </FormField>
                <FormField
                    label="Monto del devengado"
                    label-for="accrual_amount"
                    help="Ingrese el monto del devengado"
                    :errors="form.errors.accrual_amount"
                    required
                >
                    <FormControl
                        v-model="form.accrual_amount"
                        id="accrual_amount"
                        :icon="mdiCurrencyUsd"
                        autocomplete="accrual_amount"
                        type="number"
                        inputmode="decimal"
                        placeholder="Ej: 1534.35"
                        :step="0.0001"
                        :min="0"
                        :has-errors="form.errors.accrual_amount != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>

            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Estado del devengado"
                    label-for="record_status_id"
                    help="Seleccione el estado del devengado"
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
                    label="Nro. CUR del Devengado"
                    label-for="accrual_cur"
                    help="Digite el número de CUR del devengado"
                    :errors="form.errors.accrual_cur"
                    :required="form.record_status_id === STATUSES.REGISTERED"
                >
                    <FormControl
                        v-model="form.accrual_cur"
                        id="accrual_cur"
                        :icon="mdiNumeric"
                        autocomplete="accrual_cur"
                        placeholder="Ej: 123"
                        type="number"
                        :has-errors="form.errors.accrual_cur != null"
                        :disabled="
                            disabled.global.value || disabled.accrual_cur.value
                        "
                    />
                </FormField>
            </div>
            <FormField
                label="Observaciones"
                label-for="accrual_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.accrual_comments"
            >
                <FormControl
                    v-model="form.accrual_comments"
                    type="textarea"
                    id="accrual_comments"
                    :icon="mdiTag"
                    autocomplete="accrual_comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.accrual_comments != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
        </div>
        <!-- STEP 4 -->
        <div
            v-show="activePhase === STEPS.STEP_4"
            class="transition duration-500 ease-in-out"
        >
            <FormField label="Revisión de devengado">
                <FormCheckRadioGroup
                    v-model="form.treasury_approved"
                    name="treasury_approved"
                    type="radio"
                    :options="{
                        approved: 'Aprobado',
                        returned: 'Devuelto',
                        canceled: 'Cancelado',
                    }"
                    :disabled="disabled.global.value"
                />
            </FormField>
            <FormField
                label="Nro. Memorando"
                label-for="returned_document_number"
                help="Ingrese el Nro. de Memorando de la revisión de Devengado"
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
                    route-name="accruals.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </BaseButtons>
            <LabelDate
                v-if="currentOperation !== OPERATIONS.CREATE"
                :date-one="formattedDate(accrual.sec_cgf_date)"
                :date-two="formattedDate(accrual.assignment_date)"
                :date-three="formattedDate(accrual.accrual_date)"
                :date-four="formattedDate(accrual.coord_cgf_date)"
                v-model:activePhase="activePhase"
            />
        </BaseLevel>
    </CardBox>
</template>
