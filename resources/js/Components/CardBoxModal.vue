<script setup>
import { computed } from "vue";
import { mdiClose, mdiContentSaveAll, mdiBackspace, mdiPrinter } from "@mdi/js";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import CardBox from "@/components/CardBox.vue";
import OverlayLayer from "@/components/OverlayLayer.vue";
import CardBoxComponentTitle from "@/components/CardBoxComponentTitle.vue";
import NotificationBarInCard from "@/components/NotificationBarInCard.vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    button: {
        type: String,
        default: "info",
    },
    buttonLabel: {
        type: String,
        default: "Done",
    },
    hasCancel: Boolean,
    modelValue: {
        type: [String, Number, Boolean],
        default: null,
    },
    isForm: {
        type: Boolean,
        default: false,
    },
    withButton: {
        type: Boolean,
        default: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    z: {
        type: String,
        default: "z-50",
    },
});

const elementClass = computed(() => {
    const base = [
        "shadow-lg max-h-modal h-7/12 w-11/12",
        props.z ?? "",
        props.button === "danger"
            ? "md:w-2/5 lg:w-2/5 xl:w-4/12"
            : "md:w-3/5 lg:w-3/5 xl:w-7/12",
    ];
    return base;
});

const emit = defineEmits(["update:modelValue", "cancel", "confirm"]);
const value = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const confirmCancel = (mode) => {
    if (mode === "cancel") value.value = false;
    emit(mode);
};

const confirm = () => confirmCancel("confirm");

const cancel = () => confirmCancel("cancel");

window.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && value.value) {
        cancel();
    }
});
</script>

<template>
    <OverlayLayer v-show="value" @overlay-click="cancel">
        <CardBox
            v-show="value"
            :class="elementClass"
            is-modal
            :is-form="isForm"
        >
            <CardBoxComponentTitle :title="title">
                <BaseButton
                    v-if="hasCancel"
                    :icon="mdiClose"
                    color="whiteDark"
                    small
                    rounded-full
                    @click.prevent="cancel"
                />
            </CardBoxComponentTitle>

            <div class="space-y-3">
                <slot />
            </div>

            <template #footer v-if="withButton">
                <BaseButtons>
                    <BaseButton
                        :disabled="disabled"
                        type="submit"
                        :label="buttonLabel"
                        :color="disabled ? 'contrast' : button"
                        :class="{
                            'opacity-25': disabled,
                        }"
                        @click.prevent="confirm"
                        :icon="
                            buttonLabel === 'Imprimir'
                                ? mdiPrinter
                                : mdiContentSaveAll
                        "
                    />
                    <BaseButton
                        v-if="hasCancel"
                        label="Regresar"
                        color="slate"
                        @click="cancel"
                        :icon="mdiBackspace"
                    />
                </BaseButtons>
            </template>
        </CardBox>
    </OverlayLayer>
</template>
