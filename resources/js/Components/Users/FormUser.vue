<script setup>
import { computed, ref, onMounted } from "vue";
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
import FormField from "@/Components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import axios from "axios";
import FormValidationErrors from "@/Components/FormValidationErrors.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseLevel from "@/components/BaseLevel.vue";

import { ROLES, STEPS, STATUSES, OPERATIONS } from "@/Utils/constants";

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
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
});

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
    roles: optionSelect(props.roles),
};

// ---------------------------------------------------------
// FORM
// ---------------------------------------------------------
const form = useForm({
    name: "",
    email: "",
    department: "",
    role: "",
    password: "",
    password_confirmation: "",
    //   terms: [],
});

onMounted(() => completeForm(props.user));

const completeForm = (user) => {
    if (props.currentOperation !== OPERATIONS.CREATE) {
        form.name = user.name;
        form.email = user.email;
        form.department = user.department;
        form.role = user.roles[0].id;
    }
};

// const hasTermsAndPrivacyPolicyFeature = computed(
//     () => usePage().props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature
// );

// ---------------------------------------------------------
// DISABLED
// ---------------------------------------------------------
const disabled = {
    global: computed(() => props.currentOperation === OPERATIONS.SHOW),
    button: ref(false),
};
disabled.button.value =
    props.currentOperation === OPERATIONS.SHOW || form.processing;

// ---------------------------------------------------------
// USERS.STORE
// ---------------------------------------------------------
const create = () => {
    form.transform((data) => ({
        ...data,
        role: props.roles.find((role) => role.id === form.role).name,
        // terms: form.terms && form.terms.length,
    })).post(route("superadmin.users.store"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

// ---------------------------------------------------------
// COMMITMENTS.UPDATE
// ---------------------------------------------------------
const update = () => {
    form.transform((data) => ({
        ...data,
        role: props.roles.find((role) => role.id === form.role).name,
    })).put(route("superadmin.users.update", props.user.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onFinish: () => form.reset("password", "password_confirmation"),
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

// SHOW
const getUser = (id) => {
    axios
        .get(route("superadmin.users.show", id))
        .then((response) => {
            if (response) {
                console.log(response.data);
                completeForm(response.data);
            }
        })
        .catch((error) => {
            console.error(error);
        });
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

        <div
            class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
        >
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
                label="Rol"
                label-for="role"
                help="Por favor ingrese el rol a cumplir"
                :errors="form.errors.role"
            >
                <FormControl
                    v-model="form.role"
                    name="role"
                    id="role"
                    :icon="mdiAccount"
                    autocomplete="role"
                    :options="selectOptions.roles"
                    :has-errors="form.errors.role != null"
                    :disabled="disabled.global.value"
                />
            </FormField>
        </div>

        <div
            class="grid grid-cols-1 gap-x-3 lg:grid-cols-2 mb-6 lg:mb-0 last:mb-0"
        >
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
                    currentOperation === OPERATIONS.SHOW
                        ? mdiPrinter
                        : mdiContentSaveAll
                "
            />
            <BaseButton
                route-name="superadmin.users.index"
                color="slate"
                label="Regresar"
                :icon="mdiBackspace"
            />
        </BaseButtons>
    </CardBox>
</template>
