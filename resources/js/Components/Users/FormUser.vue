<script setup>
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    mdiAccount,
    mdiEmail,
    mdiFormTextboxPassword,
    mdiPrinter,
    mdiBackspace,
    mdiContentSaveAll,
} from "@mdi/js";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";
import FormValidationErrors from "@/components/FormValidationErrors.vue";
import BaseButtons from "../BaseButtons.vue";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    user: {
        type: Object,
        default: {},
    },
    roles: Object,
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

console.log(props.roles);
// ---------------------------------------------------------
// CONSTANTES
// ---------------------------------------------------------
const operations = {
    create: 1,
    show: 2,
    update: 3,
};

// ---------------------------------------------------------
// SELECTS
// ---------------------------------------------------------
// const optionSelect = (array = []) => {
//     let newArray = [];
//     let i = 0;
//     array.forEach((element) => {
//         newArray.push({
//             id: Object.values(element)[0],
//             label: Object.values(element)[1],
//         });
//     });
//     return newArray;
// };

// let selectOptions = {
//     roles: optionSelect(props.roles),
// };

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm(
    props.currentOperation === operations.create
        ? {
              name: "",
              email: "",
              department: "",
              role: "",
              password: "",
              password_confirmation: "",
              //   terms: [],
          }
        : {
              name: props.user.name,
              email: props.user.email,
              department: "", //props.user.department,
              role: "",
              password: "",
              password_confirmation: "",
              //   terms: [],
          }
);

// const hasTermsAndPrivacyPolicyFeature = computed(
//     () => usePage().props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature
// );

// ---------------------------------------------------------
// DISABLED
// ---------------------------------------------------------
const disabled = {
    global: computed(() => props.currentOperation === operations.show),
    button: ref(false),
};
disabled.button.value =
    props.currentOperation !== operations.show || form.processing;

// ---------------------------------------------------------
// USERS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
        // terms: form.terms && form.terms.length,
    })).post(route("users.store"), {
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
    })).put(route("users.update", props.user.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onFinish: () => form.reset("password", "password_confirmation"),
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
</script>

<template>
    <FormValidationErrors v-if="form.hasErrors" />

    <CardBox is-form :in-modal="inModal" @submit.prevent="transaction">
        <FormValidationErrors v-if="form.hasErrors" />

        <FormField
            label="Nombres"
            label-for="name"
            help="Ingrese sus nombres y apellidos"
            :errors="form.errors.name"
        >
            <FormControl
                v-model="form.name"
                id="name"
                :icon="mdiAccount"
                autocomplete="name"
                type="text"
                required
                :has-errors="form.errors.name != null"
            />
        </FormField>

        <FormField
            label="Correo electrónico"
            label-for="email"
            help="Ingrese su correo electrónico"
            :errors="form.errors.email"
        >
            <FormControl
                v-model="form.email"
                id="email"
                :icon="mdiEmail"
                autocomplete="email"
                type="email"
                required
                :has-errors="form.errors.email != null"
            />
        </FormField>

        <FormField
            label="Departamento"
            label-for="department"
            help="Escoja el departamento al que pertenece"
            :errors="form.errors.department"
        >
            <FormControl
                v-model="form.department"
                name="department"
                id="department"
                :icon="mdiAccount"
                autocomplete="department"
                required
                :has-errors="form.errors.department != null"
            />
        </FormField>

        <FormField
            label="Función laboral"
            label-for="role"
            help="Por favor ingrese el cargo que desempeña"
            :errors="form.errors.role"
        >
            <FormControl
                v-model="form.role"
                name="role"
                id="role"
                :icon="mdiAccount"
                autocomplete="role"
                required
                :has-errors="form.errors.role != null"
            />
        </FormField>

        <div class="grid grid-cols-1 gap-x-3 lg:grid-cols-2">
            <FormField
                label="Contraseña"
                label-for="password"
                help="Por favor, ingrese su nueva contraseña"
                :errors="form.errors.password"
            >
                <FormControl
                    v-model="form.password"
                    id="password"
                    :icon="mdiFormTextboxPassword"
                    type="password"
                    autocomplete="new-password"
                    required
                    :has-errors="form.errors.password != null"
                />
            </FormField>

            <FormField
                label="Confirmar contraseña"
                label-for="password_confirmation"
                help="Por favor, confirme su nueva contraseña"
                :errors="form.errors.password_confirmation"
            >
                <FormControl
                    v-model="form.password_confirmation"
                    id="password_confirmation"
                    :icon="mdiFormTextboxPassword"
                    type="password"
                    autocomplete="new-password"
                    required
                    :has-errors="form.errors.password_confirmation != null"
                />
            </FormField>
        </div>

        <!-- <FormCheckRadioGroup
            v-if="hasTermsAndPrivacyPolicyFeature"
            v-model="form.terms"
            name="remember"
            :options="{ agree: 'Acepto las condiciones' }"
            :has-errors="form.errors.terms != null"
        /> -->

        <BaseDivider v-if="!inModal" />
        <BaseButtons v-if="withButton">
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
                route-name="users.index"
                color="slate"
                label="Regresar"
                :icon="mdiBackspace"
            />
        </BaseButtons>
    </CardBox>
</template>
