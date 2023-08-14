<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { mdiCardAccountDetails } from "@mdi/js";
import CardBoxModal from "@/Components/CardBoxModal.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    currentOperation: {
        type: [String, Number, Boolean],
        default: 1,
    },
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
    vendor: Object,
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

// ---------------------------------------------------------
// EVENTOS DE MODAL: ABRIR Y CERRAR, CONFIRMAR O CANCELAR
// ---------------------------------------------------------
const emit = defineEmits(["update:modelValue", "update:vendor", "confirm"]);
const value = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
const newVendor = computed({
    get: () => props.vendor,
    set: (value) => emit("update:vendor", value),
});

const confirmCancel = (mode) => {
    value.value = false;
    emit(mode, mode);
};
const confirm = () => confirmCancel("confirm");

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm({ nit: "", name: "" });

// ---------------------------------------------------------
// CERTIFICATIONS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
    })).post(route("vendors.store"), {
        onError: (error) => console.log(error),
        onSuccess: () => {
            newVendor.value = {
                nit: form.nit,
                name: form.name,
            };
            form.reset();
            confirm();
        },
    });
};

const transaction = () => {
    return props.currentOperation === operations.create ? create() : "";
};
</script>

<template>
    <CardBoxModal
        v-model="value"
        title="Nuevo proveedor"
        button="success"
        button-label="Registrar"
        has-cancel
        is-form
        @confirm="transaction"
    >
        <FormField label="NIT" label-for="nit" :errors="form.errors.nit">
            <FormControl
                v-model="form.nit"
                id="nit"
                :icon="mdiCardAccountDetails"
                autocomplete="nit"
                type="text"
                placeholder="Detalle el NIT del proveedor"
                :has-errors="form.errors.nit != null"
            />
        </FormField>
        <FormField
            label="Nombre comercial"
            label-for="name"
            :errors="form.errors.name"
        >
            <FormControl
                v-model="form.name"
                id="name"
                :icon="mdiCardAccountDetails"
                autocomplete="name"
                type="text"
                placeholder="Detalle el nombre comercial de la empresa"
                :has-errors="form.errors.name != null"
            />
        </FormField>
    </CardBoxModal>
</template>
