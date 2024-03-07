<script setup>
import { mdiAccountMultiple, mdiBackspace } from "@mdi/js";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import FormUser from "@/Components/Users/FormUser.vue";
import BaseButton from "@/Components/BaseButton.vue";

// --------------------------------------------
const props = defineProps({
    user: Object,
    roles: Object,
});

const elementProps = {
    label: "Editar Usuario",
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
                    route-name="superadmin.users.index"
                    color="slate"
                    label="Regresar"
                    :icon="mdiBackspace"
                />
            </SectionTitleLineWithButton>

            <FormUser
                :user="user"
                :roles="roles"
                :current-operation="3"
                :element-props="elementProps"
            />
        </SectionMain>
    </LayoutAuthenticated>
</template>
