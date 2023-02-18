<script setup>
import { computed } from "vue";
import {
    mdiCashMinus,
    mdiCashPlus,
    mdiReceipt,
    mdiCreditCardOutline,
    mdiProgressClose,
} from "@mdi/js";
import CardBox from "@/components/CardBox.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import SpanState from "@/components/SpanState.vue";
import IconRounded from "@/components/IconRounded.vue";

const props = defineProps({
    amount: {
        type: String,
        required: true,
    },
    date: {
        type: String,
        required: true,
    },
    business: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    account: {
        type: String,
        required: true,
    },
});

const icon = computed(() => {
    if (props.type === "Pendiente de revisión") {
        return {
            icon: mdiCashMinus,
            type: "danger",
        };
    } else if (props.type === "En revisión") {
        return {
            icon: mdiCashPlus,
            type: "warning",
        };
    } else if (props.type === "Certificado") {
        return {
            icon: mdiReceipt,
            type: "green",
        };
    } else if (props.type === "Devuelto") {
        return {
            icon: mdiProgressClose,
            type: "violet",
        };
    }

    return {
        icon: mdiCreditCardOutline,
        type: "info",
    };
});
</script>

<template>
    <CardBox class="mb-6 last:mb-0" is-hoverable>
        <BaseLevel>
            <BaseLevel type="justify-start">
                <IconRounded
                    :icon="icon.icon"
                    :color="icon.type"
                    class="md:mr-6"
                />
                <div class="text-center space-y-1 md:text-left md:mr-6">
                    <h4 class="text-xl">{{ amount }}</h4>
                    <p class="text-gray-500 dark:text-slate-400">
                        <b>{{ date }}</b> @ {{ business }}
                    </p>
                </div>
            </BaseLevel>
            <div class="text-center md:text-right space-y-2">
                <p class="text-sm text-gray-500">
                    {{ name }}
                </p>
                <div>
                    <SpanState :state="type" />
                </div>
            </div>
        </BaseLevel>
    </CardBox>
</template>
