<script setup>
import { useForm, Head, Link } from "@inertiajs/vue3";
import {
    mdiAccount,
    mdiAsterisk,
    mdiLoginVariant,
    mdiLockReset,
} from "@mdi/js";
import LayoutGuest from "@/Layouts/LayoutGuest.vue";
import SectionFullScreen from "@/Components/SectionFullScreen.vue";
import CardBox from "@/Components/CardBox.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import FormValidationErrors from "@/components/FormValidationErrors.vue";
import NotificationBarInCard from "@/components/NotificationBarInCard.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";

const props = defineProps({
    canResetPassword: Boolean,
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: [],
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember && form.remember.length ? "on" : "",
    })).post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <LayoutGuest>
        <Head title="Login" />

        <SectionFullScreen v-slot="{ cardClass }" bg="slateGray">
            <CardBox :class="cardClass" is-form @submit.prevent="submit">
                <SectionTitleLineWithButton
                    title="HTMC - IESS"
                    :class="'text-center'"
                    main
                >
                    {{}}
                </SectionTitleLineWithButton>

                <FormValidationErrors v-if="form.hasErrors" />
                <FormField
                    label="Correo electrónico"
                    label-for="email"
                    help="Ingrese su correo electrónico"
                    :errors="form.errors.email"
                >
                    <FormControl
                        v-model="form.email"
                        :icon="mdiAccount"
                        id="email"
                        autocomplete="email"
                        type="email"
                        placeholder="user@dominio.com"
                        :has-errors="form.errors.email != null"
                    />
                </FormField>

                <FormField
                    label="Contraseña"
                    label-for="password"
                    help="Ingrese su contraseña"
                >
                    <FormControl
                        v-model="form.password"
                        :icon="mdiAsterisk"
                        type="password"
                        id="password"
                        placeholder="Contraseña"
                        autocomplete="current-password"
                        :has-errors="form.errors.email != null"
                    />
                </FormField>

                <FormCheckRadioGroup
                    v-model="form.remember"
                    name="remember"
                    :options="{ remember: 'Recordar' }"
                />

                <BaseDivider />

                <BaseLevel>
                    <BaseButtons>
                        <BaseButton
                            type="submit"
                            color="success"
                            label="Iniciar sesión"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            :icon="mdiLoginVariant"
                        />
                        <BaseButton
                            v-if="canResetPassword"
                            route-name="password.request"
                            color="slate"
                            label="Recuperar contraseña"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            :icon="mdiLockReset"
                        />
                        <!-- <BaseButton
                            route-name="register"
                            color="info"
                            label="Crear cuenta"
                        /> -->
                    </BaseButtons>
                </BaseLevel>
            </CardBox>
        </SectionFullScreen>
    </LayoutGuest>
</template>
