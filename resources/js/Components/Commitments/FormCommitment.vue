<script setup>
import { computed, ref } from "vue";
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
} from "@mdi/js";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import FormControlWithButton from "@/components/FormControlWithButton.vue";
import Stepper from "@/components/Stepper.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";
import BaseLevel from "@/components/BaseLevel.vue";
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
const activePhase = ref(2);
activePhase.value = role.value && role.value < 5 ? role.value : 1;

const certification = {
    contract_object: ref(""),
    vendor_id: ref(""),
    vendor_name: ref(""),
};
if (props.currentOperation === operations.update) {
    certification.contract_object.value =
        props.commitment.certification.contract_object;
    certification.vendor_id.value = props.commitment.certification.vendor_id;
    certification.vendor_name.value =
        props.commitment.certification.vendor.name;
}
const steps = [
    {
        id: 2,
        label: "Secretaría JAPC",
    },
    {
        id: 3,
        label: "Analista de Certificación",
    },
    {
        id: 4,
        label: "Coordinación General Financiera",
    },
];

// Toast
const messageResource = ref("");
const toast = ref(false);

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
    props.currentOperation === operations.create
        ? {
              certification_id: "",
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
              record_status_id: "",
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
              certification_id: props.commitment.certification_id,

              commitment_cur: props.commitment.commitment_cur,
              commitment_amount: props.commitment.commitment_amount,
              commitment_comments: props.commitment.commitment_comments,
              commitment_date:
                  props.commitment.commitment_date ??
                  new Date().toLocaleDateString(),
              vendor_id: props.commitment.vendor_id,
              record_status_id:
                  props.commitment.record_status_id &&
                  props.commitment.record_status_id <= statuses.registered
                      ? props.commitment.record_status_id
                      : "",
              treasury_approved: props.commitment.treasury_approved,
              returned_document_number:
                  props.commitment.returned_document_number,
              coord_cgf_comments: props.commitment.coord_cgf_comments,
              coord_cgf_date:
                  props.commitment.coord_cgf_date ??
                  new Date().toLocaleDateString(),
          }
);

const formSearchCertification = useForm(
    props.currentOperation === operations.create
        ? {
              certificationNumber: "",
          }
        : {
              certificationNumber:
                  props.commitment.certification.certification_number,
          }
);

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
    })).put(route("commitments.update", props.commitment.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// --------------------------------------------
// COMMITMENTS.DELETE
// --------------------------------------------
const destroy = () => {
    router.delete(`/commitments/${props.commitment.id}`, {
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
            console.log(error);
        });
};
</script>

<template>
    <Toast v-if="toast" v-model="toast" :message="messageResource" />
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
        <!-- STEP 2 -->
        <div
            v-show="activePhase === 2"
            class="transition duration-500 ease-in-out"
        >
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de asignación: </strong>
                {{ form.assignment_date }}
            </div>

            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Número de Certificación"
                    label-for="certificationNumber"
                    :errors="form.errors.certification_id"
                    help="Digite el número de certificación"
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
                                :disabled="
                                    disabled.global.value || disabledButton
                                "
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
        <!-- STEP 3 -->
        <div
            v-show="activePhase === 3"
            class="transition duration-500 ease-in-out"
        >
            <div class="gap-x-3 mb-4 text-right">
                <strong>Fecha de compromiso: </strong>
                {{ form.commitment_date }}
            </div>

            <div
                class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
            >
                <FormField
                    label="Nombre de proveedor"
                    label-for="nit_name"
                    help="Nombre del proveedor detallado en la certificación"
                >
                    <FormControl
                        v-model="certification.vendor_name.value"
                        name="nit_name"
                        id="nit_name"
                        :icon="mdiDomain"
                        autocomplete="nit_name"
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
                        :disabled="
                            disabled.global.value ||
                            disabled.record_status.value
                        "
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
                color="slate"
                label="Regresar"
                :icon="mdiBackspace"
            />
        </BaseButtons>
    </CardBox>
</template>
