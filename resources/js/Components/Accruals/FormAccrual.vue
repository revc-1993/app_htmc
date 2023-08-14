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
import CardBoxModalVendor from "@/Components/Vendors/CardBoxModalVendor.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import FormValidationErrors from "@/Components/FormValidationErrors.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import Toast from "@/Components/Toast.vue";
import LabelDate from "@/Components/LabelDate.vue";

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
        default: 1,
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
const operations = {
    create: 1,
    show: 2,
    update: 3,
};

const statuses = {
    pendingReview: 1,
    reviewing: 2,
    observed: 3,
    returned: 4,
    registered: 5,
    approved: 6,
    canceled: 7,
};

const role = computed(() => usePage().props.auth.user.roles[0].id);
const activePhase = ref(1);

const toast = ref(false);
const messageResource = ref("");

const isModalActive = ref(false);

// ---------------------------------------------------------
// SETS
// ---------------------------------------------------------
activePhase.value = role.value && role.value < 5 ? role.value : 1;

const commitment = {
    contract_administrator: ref(""),
    commitment_cur: ref(""),
    balance: ref(0),
};

if (
    (props.currentOperation === operations.show ||
        props.currentOperation === operations.update) &&
    props.accrual.commitment
) {
    commitment.contract_administrator.value =
        props.accrual.commitment.contract_administrator;
    commitment.balance.value = props.accrual.commitment.balance;
    // certification.vendor_name.value =
    //     props.commitment.certification.vendor.name ?? "";
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
const form = useForm({
    accrual_memo: "",
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
});

onMounted(() => {
    if (props.currentOperation !== operations.create) {
        form.accrual_memo = props.accrual.accrual_memo;
        form.assignment_date =
            props.accrual.assignment_date ?? new Date().toLocaleDateString();
        form.japc_comments = props.accrual.japc_comments;
        form.customer_id = props.accrual.customer_id ?? "";

        form.commitment_id = props.accrual.commitment_id;
        form.accrual_cur = props.accrual.accrual_cur;
        form.accrual_date =
            props.accrual.accrual_date ?? new Date().toLocaleDateString();
        form.record_status_id =
            props.accrual.record_status_id &&
            props.accrual.record_status_id <= statuses.registered
                ? props.accrual.record_status_id
                : "";
        form.accrual_amount = props.accrual.accrual_amount;
        form.accrual_comments = props.accrual.accrual_comments;

        form.treasury_approved = props.accrual.treasury_approved;
        form.returned_document_number = props.accrual.returned_document_number;
        form.coord_cgf_comments = props.accrual.coord_cgf_comments;
        form.coord_cgf_date =
            props.accrual.coord_cgf_date ?? new Date().toLocaleDateString();

        form.current_management = props.accrual.current_management;
    }
});

const formSearchCommitment = useForm(
    props.currentOperation === operations.create
        ? {
              commitmentNumber: "",
          }
        : {
              commitmentNumber: props.accrual.commitment
                  ? props.accrual.commitment.commitment_number
                  : "",
          }
);

// ---------------------------------------------------------
// DISABLED
// ---------------------------------------------------------
const disabled = {
    global: computed(
        () =>
            props.currentOperation === operations.show ||
            (role.value !== 5 &&
                (activePhase.value !== role.value ||
                    (props.accrual.record_status_id &&
                        props.accrual.record_status_id >= statuses.approved &&
                        role.value !== 4)))
    ),
    commitment_cur: computed(
        () => form.record_status_id !== statuses.registered
    ),
    button: ref(false),
};
disabled.button.value =
    (props.currentOperation !== operations.show &&
        role.value !== 5 &&
        (activePhase.value !== role.value ||
            (props.accrual.record_status_id &&
                props.accrual.record_status_id >= statuses.approved &&
                role.value !== 4))) ||
    form.processing ||
    formSearchCommitment.processing;

// ---------------------------------------------------------
// COMMITMENTS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
        current_management: 2,
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
            form.record_status_id > statuses.registered
                ? props.accrual.record_status_id
                : form.record_status_id,
    })).put(route("accruals.update", props.accrual.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const transaction = () => {
    return props.currentOperation === operations.create
        ? create()
        : props.currentOperation === operations.show
        ? ""
        : props.currentOperation === operations.update
        ? update()
        : "";
};

// --------------------------------------------
// BUSCAR CERTIFICACION
// --------------------------------------------
const searchCommitmentByNumber = (alert = "") => {
    axios
        .get(
            "/commitments/getCommitmentsByNumber?commitment_number=" +
                formSearchCommitment.commitmentNumber
        )
        .then((response) => {
            if (response) {
                form.clearErrors("commitment_id");
                router.reload({ only: ["FormControl"] });
                form.commitment_id = response.data.commitment.id;
                formSearchCommitment.commitmentNumber =
                    response.data.commitment.commitment_number;
                commitment.balance.value = response.data.commitment.balance;

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
            console.log(error);
        });
};

// --------------------------------------------
// MANEJO DE FECHAS
// --------------------------------------------
const formattedDate = (date) => {
    return dayjs(date).format("YYYY-MM-DD");
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
            currentOperation !== operations.create
                ? accrual.current_management
                : null
        "
    />

    <CardBox is-form :in-modal="inModal" @submit.prevent="transaction">
        <!-- STEP 1 -->
        <!-- <div
            v-show="activePhase === 1"
            class="transition duration-500 ease-in-out"
        >
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
                    label="Nro. de contrato - Convenio Marco"
                    label-for="contract_number"
                    :errors="form.errors.contract_number"
                >
                    <FormControl
                        v-model="form.contract_number"
                        id="contract_number"
                        :icon="mdiCardAccountDetails"
                        autocomplete="contract_number"
                        type="text"
                        placeholder="Detalle el Nro. de contrato del convenio marco"
                        :has-errors="form.errors.contract_number != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
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
        </div> -->
        <!-- STEP 2 -->
        <div
            v-show="activePhase === 2"
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
            v-show="activePhase === 3"
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
                    label="Monto del compromiso"
                    label-for="commitment_amount"
                    help="Ingrese el monto del compromiso"
                    :errors="form.errors.commitment_amount"
                >
                    <FormControl
                        v-model="form.commitment_amount"
                        id="commitment_amount"
                        :icon="mdiCurrencyUsd"
                        autocomplete="commitment_amount"
                        type="number"
                        inputmode="decimal"
                        placeholder="Ej: 1534.35"
                        :step="0.0001"
                        :min="0"
                        :has-errors="form.errors.commitment_amount != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>

            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Estado del compromiso"
                    label-for="record_status_id"
                    help="Seleccione el estado del compromiso"
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
                        :disabled="disabled.global.value"
                    />
                </FormField>
                <FormField
                    label="Nro. CUR del Compromiso"
                    label-for="commitment_cur"
                    help="Digite el número de CUR del compromiso"
                    :errors="form.errors.commitment_cur"
                >
                    <FormControl
                        v-model="form.commitment_cur"
                        id="commitment_cur"
                        :icon="mdiNumeric"
                        autocomplete="commitment_cur"
                        placeholder="Ej: 123"
                        type="number"
                        :has-errors="form.errors.commitment_cur != null"
                        :disabled="
                            disabled.global.value ||
                            disabled.commitment_cur.value
                        "
                    />
                </FormField>
            </div>
            <FormField
                label="Observaciones"
                label-for="commitment_comments"
                help="Máximo 255 caracteres."
                :errors="form.errors.commitment_comments"
            >
                <FormControl
                    v-model="form.commitment_comments"
                    type="textarea"
                    id="commitment_comments"
                    :icon="mdiTag"
                    autocomplete="commitment_comments"
                    placeholder="Indique las principales observaciones."
                    :has-errors="form.errors.commitment_comments != null"
                    :disabled="disabled.global.value"
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
                    :options="{
                        approved: 'Aprobado',
                        returned: 'Devuelto',
                        canceled: 'Reversado',
                    }"
                    :disabled="disabled.global.value"
                />
            </FormField>
            <FormField
                label="Nro. Memorando"
                label-for="returned_document_number"
                help="Ingrese el Nro. de Memorando de la revisión de Compromiso"
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
                        currentOperation === operations.show
                            ? mdiPrinter
                            : mdiContentSaveAll
                    "
                />
                <BaseButton
                    route-name="commitments.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </BaseButtons>
            <LabelDate
                v-if="currentOperation !== 1"
                :dateOne="formattedDate(commitment.sec_cgf_date)"
                :dateTwo="formattedDate(commitment.assignment_date)"
                :dateThree="formattedDate(commitment.commitment_date)"
                :dateFour="formattedDate(commitment.coord_cgf_date)"
                v-model:activePhase="activePhase"
            />
        </BaseLevel>
    </CardBox>
</template>
