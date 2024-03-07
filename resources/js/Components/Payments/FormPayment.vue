<script setup>
import { computed, ref } from "vue";
import axios from "axios";
import { useForm, usePage } from "@inertiajs/vue3";
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
import CardBox from "@/components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import Stepper from "@/Components/Stepper.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import FormValidationErrors from "@/Components/FormValidationErrors.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import Toast from "@/Components/Toast.vue";
import LabelDate from "@/Components/LabelDate.vue";

import {
    ROLES,
    STEPS,
    STATUSES,
    OPERATIONS,
    ANALYST_STATUSES,
    MANAGER_STATUSES,
} from "@/Utils/constants";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime"; // Importa el complemento de fechas relativas
import "dayjs/locale/es"; // Si deseas utilizar el idioma español

dayjs.locale("es");
dayjs.extend(relativeTime);

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    payment: {
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
        : props.payment &&
          props.payment.current_management &&
          props.currentOperation === OPERATIONS.SHOW
        ? props.payment.current_management.step
        : props.payment &&
          props.payment.current_management &&
          props.currentOperation === OPERATIONS.UPDATE
        ? props.payment.current_management
        : STEPS.STEP_1;

const accrual = {
    accrual_cur: ref(""),
    accrual_amount: ref(""),
    process_number: ref(""),
    voucher_number: ref(""),
};

if (
    (props.currentOperation === OPERATIONS.SHOW ||
        props.currentOperation === OPERATIONS.UPDATE) &&
    props.payment.accrual
) {
    accrual.accrual_cur = props.payment.accrual.accrual_cur;
    accrual.accrual_amount = props.payment.accrual.accrual_amount;
    accrual.process_number = props.payment.accrual.commitment.process_number;
    accrual.voucher_number = props.payment.accrual.voucher_number;
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
              accrual_id: "",
              customer_id: "",
              manager_status: "",
              manager_comments: "",
              manager_date: new Date().toLocaleDateString(),
              treasury_approved: "",

              analyst_status: "",
              analyst_comments: "",
              analyst_date: new Date().toLocaleDateString(),

              current_management: "",
          }
        : {
              accrual_id: props.payment.accrual_id,
              customer_id: props.payment.customer_id,
              manager_status: props.payment.manager_status,
              manager_comments: props.payment.manager_comments,
              manager_date: props.payment.manager_date,
              treasury_approved: props.payment.treasury_approved,
              analyst_status:
                  props.currentOperation === OPERATIONS.SHOW
                      ? props.payment.analyst_status.id
                      : props.payment.analyst_status,
              analyst_comments: props.payment.analyst_comments,
              analyst_date: props.payment.analyst_date,

              current_management: props.payment.current_management,
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
            // 2. ESTATUS PAGADO
            (props.payment.manager_status &&
                props.payment.manager_status === MANAGER_STATUSES.PAID) ||
            // 3.
            // 3.1. ROL NO SEA ADMIN
            (role.name !== ROLES.ADMIN &&
                // 3.2.1. Y, QUE NO ESTÉ EN EL FORMULARIO QUE LE CORRESPONDE
                activePhase.value !== role.step)
    ),
    button: ref(false),
};
// CONDICIONES PARA BLOQUEO DE BOTON DE ENVIAR
disabled.button.value =
    (props.currentOperation !== OPERATIONS.SHOW &&
        role.name !== ROLES.ADMIN &&
        (activePhase.value !== role.step ||
            (props.payment.manager_status &&
                props.payment.manager_status === MANAGER_STATUSES.PAID))) ||
    form.processing;

// ---------------------------------------------------------
// COMMITMENTS.UPDATE
// ---------------------------------------------------------
const update = () => {
    form.transform((data) => ({
        ...data,
        manager_status:
            form.manager_status > STATUSES.REGISTERED
                ? props.payment.manager_status
                : form.manager_status,
    })).put(route("payments.update", props.payment.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const transaction = () => {
    return props.currentOperation === OPERATIONS.SHOW
        ? ""
        : props.currentOperation === OPERATIONS.UPDATE
        ? update()
        : "";
};

// --------------------------------------------
// MANEJO DE FECHAS
// --------------------------------------------
const formattedDate = (date = null) => {
    if (!date) return dayjs().format("YYYY-MM-DD HH:mm:ss");
    else return dayjs(date).format("YYYY-MM-DD HH:mm:ss");
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
                ? payment.current_management
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
                <FormField label="CUR devengado" label-for="accrual_cur">
                    <FormControl
                        v-model="accrual.accrual_cur"
                        id="accrual_cur"
                        name="accrual_cur"
                        :icon="mdiCardAccountDetails"
                        placeholder="CUR devengado"
                        autocomplete="accrual_cur"
                        type="text"
                        disabled
                    />
                </FormField>
                <FormField label="Nro. de proceso" label-for="process_number">
                    <FormControl
                        v-model="accrual.process_number"
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
                    label="Monto del devengado"
                    label-for="accrual_amount"
                    help="Monto del devengado"
                >
                    <FormControl
                        v-model="accrual.accrual_amount"
                        name="accrual_amount"
                        id="accrual_amount"
                        :icon="mdiCurrencyUsd"
                        autocomplete="accrual_amount"
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
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
                v-show="
                    form.analyst_status === ANALYST_STATUSES.REVIEWED ||
                    form.analyst_status === ANALYST_STATUSES.RETURNED
                "
            >
                <FormField label="Revisión de devengado">
                    <FormCheckRadioGroup
                        v-model="form.treasury_approved"
                        name="treasury_approved"
                        type="radio"
                        :options="{
                            paid: 'Pagado',
                            returned: 'Devuelto',
                            in_review: 'En subsanación',
                        }"
                        :disabled="disabled.global.value"
                    />
                </FormField>
                <div></div>
            </div>
            <FormField
                label="Observaciones"
                label-for="manager_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.manager_comments"
            >
                <FormControl
                    v-model="form.manager_comments"
                    type="textarea"
                    id="manager_comments"
                    :icon="mdiTag"
                    autocomplete="manager_comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.manager_comments != null"
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
                    label="Nro. de Comprobante"
                    label-for="voucher_number"
                >
                    <FormControl
                        v-model="accrual.voucher_number"
                        id="voucher_number"
                        name="voucher_number"
                        :icon="mdiCardAccountDetails"
                        placeholder="CUR devengado"
                        autocomplete="voucher_number"
                        type="text"
                        disabled
                    />
                </FormField>
                <FormField
                    label="Estado del pago"
                    label-for="analyst_status"
                    help="Seleccione el estado del expediente analizado"
                    :errors="form.errors.analyst_status"
                    required
                >
                    <FormControl
                        v-model="form.analyst_status"
                        name="analyst_status"
                        id="analyst_status"
                        :icon="mdiDomain"
                        autocomplete="analyst_status"
                        :options="selectOptions.recordStatus"
                        :has-errors="form.errors.analyst_status != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <FormField
                label="Observaciones"
                label-for="analyst_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.analyst_comments"
            >
                <FormControl
                    v-model="form.analyst_comments"
                    type="textarea"
                    id="analyst_comments"
                    :icon="mdiTag"
                    autocomplete="analyst_comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.analyst_comments != null"
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
                    route-name="payments.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </BaseButtons>
            <LabelDate
                v-if="currentOperation !== OPERATIONS.CREATE"
                :date-one="formattedDate(payment.manager_date)"
                :date-two="formattedDate(payment.analyst_date)"
                :date-three="formattedDate()"
                :date-four="formattedDate()"
                v-model:activePhase="activePhase"
            />
        </BaseLevel>
    </CardBox>
</template>
