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
    commitment: {
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
// PROPIEDADES
// ---------------------------------------------------------
const role = usePage().props.auth.user.roles;
const activePhase = ref(STEPS.STEP_1);

const toast = ref(false);
const messageResource = ref("");

const isModalActive = ref(false);
const newVendor = ref({});

// ---------------------------------------------------------
// SETS
// ---------------------------------------------------------
activePhase.value =
    role.name !== ROLES.ADMIN
        ? role.step
        : props.commitment &&
          props.commitment.current_management &&
          props.currentOperation === OPERATIONS.SHOW
        ? props.commitment.current_management.step
        : props.commitment &&
          props.commitment.current_management &&
          props.currentOperation === OPERATIONS.UPDATE
        ? props.commitment.current_management
        : STEPS.STEP_1;

const certification = {
    contract_object: ref(""),
    vendor_id: ref(""),
    vendor_name: ref(""),
    balance: ref(0),
};

if (
    (props.currentOperation === OPERATIONS.SHOW ||
        props.currentOperation === OPERATIONS.UPDATE) &&
    props.commitment.certification
) {
    certification.contract_object.value =
        props.commitment.certification?.contract_object;
    // certification.vendor_id.value = props.commitment.certification.vendor_id;
    certification.balance.value = props.commitment.certification.balance;
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
              certification_id: "",
              commitment_memo: "",
              process_number: "",
              purchase_order: "",
              contract_administrator_id: "",
              contract_number: "",
              sec_cgf_date: new Date().toLocaleDateString(),
              sec_cgf_comments: "",

              assignment_date: new Date().toLocaleDateString(),
              japc_comments: "",
              customer_id: "",

              commitment_cur: "",
              commitment_amount: "",
              commitment_comments: "",
              commitment_date: new Date().toLocaleDateString(),
              vendor_id: "",
              record_status_id: "",
              treasury_approved: "",
              returned_document_number: "",
              coord_cgf_comments: "",
              coord_cgf_date: new Date().toLocaleDateString(),
              current_management: "",
          }
        : {
              certification_id: props.commitment.certification_id,
              commitment_memo: props.commitment.commitment_memo,
              process_number: props.commitment.process_number,
              purchase_order: props.commitment.purchase_order,
              contract_number: props.commitment.contract_number,
              contract_administrator_id:
                  props.commitment.contract_administrator_id,
              sec_cgf_comments: props.commitment.sec_cgf_comments,
              sec_cgf_date: props.commitment.sec_cgf_date,
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
              record_status_id:
                  props.commitment.record_status_id &&
                  props.commitment.record_status_id <= STATUSES.REGISTERED
                      ? props.commitment.record_status_id
                      : "",
              treasury_approved: props.commitment.treasury_approved,
              returned_document_number:
                  props.commitment.returned_document_number,
              coord_cgf_comments: props.commitment.coord_cgf_comments,
              coord_cgf_date:
                  props.commitment.coord_cgf_date ??
                  new Date().toLocaleDateString(),
              current_management: props.commitment.current_management,
          }
);

const formSearchCertification = useForm(
    props.currentOperation === OPERATIONS.CREATE
        ? {
              certificationNumber: "",
          }
        : {
              certificationNumber:
                  props.commitment.certification?.certification_number,
          }
);

const formSearchVendor = useForm(
    props.currentOperation === OPERATIONS.CREATE
        ? {
              nit: "",
              name: "",
          }
        : {
              nit: props.commitment.vendor?.nit,
              name: props.commitment.vendor?.name,
          }
);

const formSearchContractAdministrator = useForm(
    props.currentOperation === OPERATIONS.CREATE
        ? {
              ci: "",
              names: "",
          }
        : {
              ci: props.commitment.contract_administrator?.ci,
              names: props.commitment.contract_administrator?.names,
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
            (props.commitment.record_status_id &&
                props.commitment.record_status_id >= STATUSES.CANCELED) ||
            // 3.
            // 3.1. ROL NO SEA ADMIN
            (role.name !== ROLES.ADMIN &&
                // 3.2.1. Y, QUE NO ESTÉ EN EL FORMULARIO QUE LE CORRESPONDE
                (activePhase.value !== role.step ||
                    // 3.2.2. O QUE ESTATUS SEA APROBADO U OTRO
                    (props.commitment.record_status_id &&
                        props.commitment.record_status_id >=
                            STATUSES.APPROVED)))
    ),
    // CONDICIONES PARA BLOQUEO DE INPUT CERTIFICATION
    // 1. CUANDO NO SEA IGUAL A REGISTRADO
    commitment_cur: computed(
        () => form.record_status_id !== STATUSES.REGISTERED
    ),
    button: ref(false),
};
// CONDICIONES PARA BLOQUEO DE BOTON DE ENVIAR
disabled.button.value =
    (props.currentOperation !== OPERATIONS.SHOW &&
        role.name !== ROLES.ADMIN &&
        (activePhase.value !== role.step ||
            (props.commitment.record_status_id &&
                props.commitment.record_status_id >= STATUSES.APPROVED))) ||
    form.processing ||
    formSearchCertification.processing ||
    formSearchContractAdministrator.processing;

// ---------------------------------------------------------
// COMMITMENTS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
        current_management: STEPS.STEP_1,
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
        record_status_id:
            form.record_status_id > STATUSES.REGISTERED
                ? props.commitment.record_status_id
                : form.record_status_id,
    })).put(route("commitments.update", props.commitment.id), {
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
const searchCertificationByNumber = (alert = "") => {
    axios
        .get(
            "/certifications/getCertificationByNumber?certification_number=" +
                formSearchCertification.certificationNumber
        )
        .then((response) => {
            if (response) {
                form.clearErrors("certification_id");
                router.reload({ only: ["FormControl"] });
                form.certification_id = response.data.certification.id;
                formSearchCertification.certificationNumber =
                    response.data.certification.certification_number;
                certification.contract_object.value =
                    response.data.certification.contract_object;
                certification.balance.value =
                    response.data.certification.balance;

                if (alert === "alert") {
                    toast.value = true;
                    messageResource.value = {
                        response:
                            "Se encontró la Certificación Nro. " +
                            formSearchCertification.certificationNumber,
                        operation: 1,
                    };
                }
            }
        })
        .catch((error) => {
            form.setError(
                "certification_id",
                "No se encontró este Nro. de Certificación."
            );
            router.reload({ only: ["FormControl"] });
            if (form.certification_id !== null) form.certification_id = null;
            certification.contract_object.value = "";
            certification.balance.value = "";
            console.log(error);
        });
};

// --------------------------------------------
// BUSCAR PROVEEDOR
// --------------------------------------------
const searchVendorByNit = (alert = "") => {
    axios
        .get("/vendors/getVendorByNit?nit=" + formSearchVendor.nit)
        .then((response) => {
            if (response) {
                form.clearErrors("vendor_id");
                router.reload({ only: ["FormControl"] });
                form.vendor_id = response.data.vendor.id;
                formSearchVendor.nit = response.data.vendor.nit;
                formSearchVendor.name = response.data.vendor.name;

                if (alert === "alert") {
                    toast.value = true;
                    messageResource.value = {
                        response:
                            "Se encontró el proveedor con NIT " +
                            formSearchVendor.nit,
                        operation: 1,
                    };
                }
            }
        })
        .catch((error) => {
            form.setError(
                "vendor_id",
                "No se encontró este NIT en los registros."
            );
            router.reload({ only: ["FormControl"] });
            form.vendor_id = null;
            formSearchVendor.name = "";
            console.log(error);
        });
};

// --------------------------------------------
// BUSCAR ADMINISTRADOR DE CONTRATO
// --------------------------------------------
const searchContractAdministratorByCi = (alert = "") => {
    axios
        .get(
            "/contractAdministrators/getContractAdministratorByCi?ci=" +
                formSearchContractAdministrator.ci
        )
        .then((response) => {
            if (response) {
                form.clearErrors("contract_administrator_id");
                router.reload({ only: ["FormControl"] });
                form.contract_administrator_id =
                    response.data.contractAdministrator.id;
                formSearchContractAdministrator.ci =
                    response.data.contractAdministrator.ci;
                formSearchContractAdministrator.names =
                    response.data.contractAdministrator.names;

                if (alert === "alert") {
                    toast.value = true;
                    messageResource.value = {
                        response:
                            "Se encontró el administrador de contrato con C.I. " +
                            formSearchContractAdministrator.ci,
                        operation: 1,
                    };
                }
            }
        })
        .catch((error) => {
            form.setError(
                "contract_administrator_id",
                "No se encontró este administrador de contrato en los registros."
            );
            router.reload({ only: ["FormControl"] });
            form.contract_administrator_id = null;
            formSearchContractAdministrator.ci = "";
            formSearchContractAdministrator.names = "";
            console.log(error);
        });
};

// --------------------------------------------
// MODAL DE CREAR PROVEEDOR: 1 Create, 2 Show, 3 Update, 4 Delete
// --------------------------------------------
const openModal = () => {
    isModalActive.value = true;
};

const closeModal = (event) => {
    if (event === "confirm") {
        formSearchVendor.nit = newVendor.value.nit;
        searchVendorByNit();
        toast.value = true;
        messageResource.value = {
            response: "Proveedor creado exitosamente.",
            operation: 1,
        };
    }
    isModalActive.value = false;
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
    <CardBoxModalVendor
        v-model="isModalActive"
        v-model:vendor="newVendor"
        :current-operation="OPERATIONS.CREATE"
        @confirm="closeModal"
    />

    <Toast v-if="toast" v-model="toast" :message="messageResource" />
    <FormValidationErrors v-if="form.hasErrors" />

    <Stepper
        v-model="activePhase"
        :steps="roles"
        :operation="currentOperation"
        :current-management="
            currentOperation !== OPERATIONS.CREATE
                ? commitment.current_management
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
                    label="Nro. de contrato - Convenio Marco"
                    label-for="contract_number"
                    :errors="form.errors.contract_number"
                    required
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
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Nro. Proceso"
                    label-for="process_number"
                    help="Ingrese el número del Proceso"
                    :errors="form.errors.process_number"
                    required
                >
                    <FormControl
                        v-model="form.process_number"
                        id="process_number"
                        :icon="mdiNumeric"
                        autocomplete="process_number"
                        type="text"
                        placeholder="Ej: IC-HTMC-084-2022"
                        :has-errors="form.errors.process_number != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
                <FormField
                    label="Orden de compra"
                    label-for="purchase_order"
                    help="Ingrese el número de orden de compra, si es necesario"
                    :errors="form.errors.purchase_order"
                >
                    <FormControl
                        v-model="form.purchase_order"
                        id="purchase_order"
                        :icon="mdiNumeric"
                        autocomplete="purchase_order"
                        type="text"
                        placeholder="Ej: CE-2023000384849"
                        :has-errors="form.errors.purchase_order != null"
                        :disabled="disabled.global.value"
                    />
                </FormField>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Administrador de contrato"
                    label-for="ci"
                    :errors="form.errors.contract_administrator_id"
                    help="Digite el número de cédula del administrador de contratou orden de compra"
                    required
                >
                    <form
                        @submit.prevent="
                            searchContractAdministratorByCi('alert')
                        "
                    >
                        <FormControlWithButton
                            v-model="formSearchContractAdministrator.ci"
                            id="ci"
                            name="ci"
                            :icon="mdiCardAccountDetails"
                            autocomplete="ci"
                            type="text"
                            placeholder="Digite el número de cédula"
                            :has-errors="
                                form.errors.contract_administrator_id != null
                            "
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
                    label="Nombres de Administrador de Contrato"
                    label-for="names"
                >
                    <FormControl
                        v-model="formSearchContractAdministrator.names"
                        id="names"
                        name="names"
                        :icon="mdiCardAccountDetails"
                        placeholder="Nombres de administrador de contrato"
                        autocomplete="names"
                        type="text"
                        disabled
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
                    label="Nro. Proceso"
                    label-for="process_number"
                    help="Ingrese el número del Proceso"
                    :errors="form.errors.process_number"
                    required
                >
                    <FormControl
                        v-model="form.process_number"
                        id="process_number"
                        :icon="mdiNumeric"
                        autocomplete="process_number"
                        type="text"
                        placeholder="Ej: IC-HTMC-084-2022"
                        :has-errors="form.errors.process_number != null"
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
        <div
            v-show="activePhase === STEPS.STEP_3"
            class="transition duration-500 ease-in-out"
        >
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="NIT de Proveedor"
                    label-for="nit"
                    :errors="form.errors.vendor_id"
                    help="Digite el NIT del proveedor"
                    required
                >
                    <form @submit.prevent="searchVendorByNit('alert')">
                        <FormControlWithButton
                            v-model="formSearchVendor.nit"
                            id="nit"
                            name="nit"
                            :icon="mdiCardAccountDetails"
                            autocomplete="nit"
                            type="text"
                            placeholder="Detalle el NIT del Proveedor"
                            :has-errors="form.errors.vendor_id != null"
                            :disabled="disabled.global.value"
                        >
                            <BaseButton
                                type="submit"
                                color="info"
                                :icon="mdiMagnify"
                                tooltip="Buscar"
                                small
                                location="center"
                                :disabled="disabled.global.value"
                            />
                            <BaseButton
                                color="success"
                                :icon="mdiBriefcasePlus"
                                tooltip="Nuevo proveedor"
                                small
                                location="end"
                                :disabled="disabled.global.value"
                                @click="openModal"
                            />
                        </FormControlWithButton>
                    </form>
                </FormField>
                <FormField label="Nombre de proveedor" label-for="name">
                    <FormControl
                        v-model="formSearchVendor.name"
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
                    label="Número de Certificación"
                    label-for="certificationNumber"
                    :errors="form.errors.certification_id"
                    help="Digite el número de certificación"
                    required
                >
                    <form
                        @submit.prevent="searchCertificationByNumber('alert')"
                    >
                        <FormControlWithButton
                            v-model="
                                formSearchCertification.certificationNumber
                            "
                            id="certificationNumber"
                            name="certificationNumber"
                            :icon="mdiCardAccountDetails"
                            autocomplete="certificationNumber"
                            type="text"
                            placeholder="Digite el número de certificación"
                            :has-errors="form.errors.certification_id != null"
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
                    label="Objeto de contrato"
                    label-for="contract_object"
                >
                    <FormControl
                        v-model="certification.contract_object.value"
                        id="contract_object"
                        name="contract_object"
                        :icon="mdiCardAccountDetails"
                        placeholder="Objeto de contrato"
                        autocomplete="contract_object"
                        type="text"
                        disabled
                    />
                </FormField>
            </div>
            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Saldo de certificación"
                    label-for="certification_balance"
                    help="Saldo de la certificación seleccionada"
                >
                    <FormControl
                        v-model="certification.balance.value"
                        name="certification_balance"
                        id="certification_balance"
                        :icon="mdiCurrencyUsd"
                        autocomplete="certification_balance"
                        disabled
                    />
                </FormField>
                <FormField
                    label="Monto del compromiso"
                    label-for="commitment_amount"
                    help="Ingrese el monto del compromiso"
                    :errors="form.errors.commitment_amount"
                    :required="form.record_status_id === STATUSES.REGISTERED"
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
                    label="Nro. CUR del Compromiso"
                    label-for="commitment_cur"
                    help="Digite el número de CUR del compromiso"
                    :errors="form.errors.commitment_cur"
                    :required="form.record_status_id === STATUSES.REGISTERED"
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
            v-show="activePhase === STEPS.STEP_4"
            class="transition duration-500 ease-in-out"
        >
            <FormField
                label="Revisión de compromiso"
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
                    route-name="commitments.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </BaseButtons>
            <LabelDate
                v-if="currentOperation !== OPERATIONS.CREATE"
                :date-one="formattedDate(commitment.sec_cgf_date)"
                :date-two="formattedDate(commitment.assignment_date)"
                :date-three="formattedDate(commitment.commitment_date)"
                :date-four="formattedDate(commitment.coord_cgf_date)"
                v-model:activePhase="activePhase"
            />
        </BaseLevel>
    </CardBox>
</template>
