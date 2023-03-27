<script setup>
import { useForm, usePage, Head } from "@inertiajs/vue3";
import { computed } from "vue";
import { mdiAccount, mdiEmail, mdiFormTextboxPassword } from "@mdi/js";
import LayoutGuest from "@/layouts/LayoutGuest.vue";
import SectionFullScreen from "@/components/SectionFullScreen.vue";
import CardBox from "@/components/CardBox.vue";
import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import FormValidationErrors from "@/components/FormValidationErrors.vue";

const props = defineProps({
    departments: Object,
    role: Object,
});

const form = useForm({
    name: "",
    email: "",
    department: "",
    role: "",
    password: "",
    password_confirmation: "",
    terms: [],
});

const hasTermsAndPrivacyPolicyFeature = computed(
    () => usePage().props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature
);

const submit = () => {
    form.transform((data) => ({
        ...data,
        terms: form.terms && form.terms.length,
    })).post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

let departments = [];
let role = [];

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
    department: optionSelect(props.departments, departments),
    role: optionSelect(props.role, role),
};
</script>

<template>
    <LayoutGuest>
        <Head title="Register" />

        <SectionFullScreen v-slot="{ cardClass }" bg="slateGray">
            <CardBox :class="cardClass" is-form @submit.prevent="submit">
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
                        :options="selectOptions.department"
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
                        :options="selectOptions.role"
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
                            :has-errors="
                                form.errors.password_confirmation != null
                            "
                        />
                    </FormField>
                </div>

                <FormCheckRadioGroup
                    v-if="hasTermsAndPrivacyPolicyFeature"
                    v-model="form.terms"
                    name="remember"
                    :options="{ agree: 'Acepto las condiciones' }"
                    :has-errors="form.errors.terms != null"
                />

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
                        route-name="certifications.index"
                        color="slate"
                        label="Regresar"
                        :icon="mdiBackspace"
                    />
                </BaseButtons>
            </CardBox>
        </SectionFullScreen>
    </LayoutGuest>
</template>
