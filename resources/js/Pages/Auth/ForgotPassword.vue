<script setup>
import { useForm, Head, Link } from "@inertiajs/vue3";
import { mdiEmail } from "@mdi/js";
import LayoutGuest from "@/layouts/LayoutGuest.vue";
import SectionFullScreen from "@/components/SectionFullScreen.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import BaseButton from "@/components/BaseButton.vue";
import FormValidationErrors from "@/components/FormValidationErrors.vue";
import NotificationBarInCard from "@/components/NotificationBarInCard.vue";
import BaseLevel from "@/components/BaseLevel.vue";

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <LayoutGuest>
        <Head title="Recuperación de cuenta" />

        <SectionFullScreen v-slot="{ cardClass }" bg="slateGray">
            <CardBox :class="cardClass" is-form @submit.prevent="submit">
                <FormValidationErrors />

                <NotificationBarInCard v-if="status" color="info">
                    {{ status }}
                </NotificationBarInCard>

                <FormField>
                    <div class="mb-4 text-sm text-gray-600">
                        ¿Ha olvidado su contraseña? No se preocupe. Díganos su
                        dirección de correo electrónico y le enviaremos un
                        enlace que le permitirá elegir una nueva.
                    </div>
                </FormField>

                <FormField
                    label="Correo electrónico"
                    help="Por favor, ingrese su correo electrónico."
                >
                    <FormControl
                        v-model="form.email"
                        :icon="mdiEmail"
                        autocomplete="email"
                        type="email"
                        required
                    />
                </FormField>

                <BaseDivider />

                <BaseLevel>
                    <BaseButton
                        type="submit"
                        color="info"
                        label="Recuperar"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    />
                    <Link :href="route('login')"> Iniciar sesión </Link>
                </BaseLevel>
            </CardBox>
        </SectionFullScreen>
    </LayoutGuest>
</template>
