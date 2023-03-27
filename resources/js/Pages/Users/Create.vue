<script setup>
import { mdiAccountMultiple, mdiBackspace } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import FormUser from "@/components/users/FormUser.vue";
import BaseButton from "@/components/BaseButton.vue";

// --------------------------------------------
const props = defineProps({
    roles: Object,
});

const elementProps = {
    label: "Crear Usuario",
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="elementProps.label" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAccountMultiple"
                :title="elementProps.label"
                main
            >
                <BaseButton
                    route-name="users.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </SectionTitleLineWithButton>

            <FormUser
                :roles="roles"
                :current-operation="1"
                :element-props="elementProps"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
