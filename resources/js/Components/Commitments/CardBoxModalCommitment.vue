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
import Step from "@/components/Step.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    instance: String,
    commitment: Object,
    operation: String,
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
});

const isNotRegister = computed(() => {
    typeof props.commitment.management_status !== "undefined";
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
        2: "Ver ",
        3: "Actualizar ",
        4: "Eliminar ",
    }[props.operation] + props.instance;

const button = computed(() => {
    return {
        2: "info",
        3: "success",
        4: "danger",
    }[props.operation];
});

const isShow = props.operation === "2";
const isUpdate = props.operation === "3";

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm(
    props.operation === "1"
        ? {
              process_code: "",
              vendor_name: "",
              contract_administrator: "",
              amount_to_commit: "",
              management_status: "",
              comments: "",
          }
        : {
              process_code: props.commitment.process_code,
              vendor_name: props.commitment.vendor_name,
              contract_administrator: props.commitment.contract_administrator,
              amount_to_commit: props.commitment.amount_to_commit,
              management_status: props.commitment.management_status,
              comments: props.commitment.comments,
          }
);

// ---------------------------------------------------------
// CERTIFICATIONS.UPDATE
// ---------------------------------------------------------
const update = () => {
    form.transform((data) => ({
        ...data,
    })).put(route("commitments.update", props.commitment.id), {
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
    router.delete(`/commitments/${props.commitment.id}`, {
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

            <BaseDivider />

            <Step v-if="value" :state="commitment.management_status ?? ''" />

            <FormField>
                <FormField
                    label="Cod. Proceso"
                    label-for="process_code"
                    help="Ingrese el Código de Proceso"
                    :errors="form.errors.process_code"
                >
                    <FormControl
                        v-model="form.process_code"
                        id="process_code"
                        :icon="mdiNumeric"
                        autocomplete="process_code"
                        type="text"
                        placeholder="Ej: IE-RER-CM-43"
                        :has-errors="form.errors.process_code != null"
                        :disabled="!isUpdate"
                    />
                </FormField>
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
                        placeholder="Detalle el nombre del administrador del contrato"
                        :has-errors="form.errors.contract_administrator != null"
                        :disabled="!isUpdate"
                    />
                </FormField>
            </FormField>
            <FormField>
                <FormField
                    label="Nombre de proveedor"
                    label-for="vendor_name"
                    :errors="form.errors.vendor_name"
                >
                    <FormControl
                        v-model="form.vendor_name"
                        id="vendor_name"
                        :icon="mdiCardAccountDetails"
                        autocomplete="vendor_name"
                        type="text"
                        placeholder="Detalle el nombre del proveedor"
                        :has-errors="form.errors.vendor_name != null"
                        :disabled="!isUpdate"
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
                        :disabled="!isUpdate"
                    />
                </FormField>
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
