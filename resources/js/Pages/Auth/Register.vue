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

const form = useForm({
    name: "",
    email: "",
    department: "",
    job_position: "",
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
</script>

<template>
    <LayoutGuest>
        <Head title="Register" />

        <SectionFullScreen v-slot="{ cardClass }" bg="slateGray">
            <CardBox
                :class="cardClass"
                class="my-24"
                is-form
                @submit.prevent="submit"
            >
                <FormValidationErrors v-if="form.hasErrors" />

                <FormField
                    label="Nombres"
                    label-for="name"
                    help="Por favor ingrese sus nombres"
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
                    help="Por favor, ingrese su correo electrónico"
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
                    help="Por favor ingrese el departamento al que pertenece"
                    :errors="form.errors.department"
                >
                    <FormControl
                        v-model="form.department"
                        name="department"
                        id="department"
                        :icon="mdiAccount"
                        autocomplete="department"
                        :options="selectOptions.requestingArea"
                        required
                        :has-errors="form.errors.department != null"
                    />
                    <div v-if="form.errors.department">
                        {{ form.errors.department }}
                    </div>
                </FormField>

                <FormField
                    label="Función laboral"
                    label-for="job_position"
                    help="Por favor ingrese el cargo que desempeña"
                    :errors="form.errors.job_position"
                >
                    <FormControl
                        v-model="form.job_position"
                        id="job_position"
                        :icon="mdiAccount"
                        autocomplete="job_position"
                        type="text"
                        required
                    />
                    <div v-if="form.errors.job_position">
                        {{ form.errors.job_position }}
                    </div>
                </FormField>

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
                    />
                    <div v-if="form.errors.password">
                        {{ form.errors.password }}
                    </div>
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
                    />
                    <div v-if="form.errors.password_confirmation">
                        {{ form.errors.password_confirmation }}
                    </div>
                </FormField>

                <FormCheckRadioGroup
                    v-if="hasTermsAndPrivacyPolicyFeature"
                    v-model="form.terms"
                    name="remember"
                    :options="{ agree: 'Acepto las condiciones' }"
                />
                <div
                    v-if="form.errors.terms"
                    class="text-sm text-red-500 dark:text-red-400 mt-1"
                >
                    * {{ form.errors.terms }}
                </div>

                <BaseDivider />

                <BaseButtons>
                    <BaseButton
                        type="submit"
                        color="info"
                        label="Crear usuario"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    />
                    <BaseButton
                        route-name="login"
                        color="info"
                        outline
                        label="Iniciar sesión"
                    />
                </BaseButtons>
            </CardBox>
        </SectionFullScreen>
    </LayoutGuest>
</template>
