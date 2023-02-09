<script setup>
import { ref, computed, useSlots } from "vue";
import { mdiClose } from "@mdi/js";
import { colorsBgLight, colorsOutline } from "@/colors.js";
import BaseLevel from "@/components/BaseLevel.vue";
import BaseIcon from "@/components/BaseIcon.vue";
import BaseButton from "@/components/BaseButton.vue";
import OverlayLayer from "@/components/OverlayLayer.vue";

const props = defineProps({
    icon: {
        type: String,
        default: null,
    },
    outline: Boolean,
    color: {
        type: String,
        required: true,
    },
    dismissed: Boolean,
});

const componentClass = computed(() =>
    props.outline ? colorsOutline[props.color] : colorsBgLight[props.color]
);

const isDismissed = ref(false);

const dismiss = () => {
    isDismissed.value = true;
};

const slots = useSlots();

const hasRightSlot = computed(() => slots.right);

setTimeout(() => dismiss(), 3000);
</script>

<template>
    <transition name="bounce" appear>
        <div
            v-if="!isDismissed"
            :class="componentClass"
            class="px-3 py-4 md:py-2 mb-6 last:mb-0 border rounded-lg transition-colors duration-150"
        >
            <BaseLevel>
                <div class="flex flex-col md:flex-row items-center">
                    <BaseIcon
                        v-if="icon"
                        :path="icon"
                        w="w-10 md:w-5"
                        h="h-10 md:h-5"
                        size="24"
                        class="md:mr-2"
                    />
                    <span class="text-center md:text-left md:py-2"
                        ><slot
                    /></span>
                </div>
                <slot v-if="hasRightSlot" name="right" />
                <BaseButton
                    v-else
                    :icon="mdiClose"
                    small
                    rounded-full
                    color="white"
                    @click="dismiss"
                />
            </BaseLevel>
        </div>
    </transition>
</template>

<style>
.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity: 0;
}
.slide-fade-enter-active {
    transition: opacity 0.7s ease;
}
.slide-fade-leave-active {
    transition: opacity 2s ease;
}

.bounce-enter-active {
    animation: bounce-in 0.2s;
}
.bounce-leave-active {
    animation: bounce-out 0.5s reverse;
}
@keyframes bounce-in {
    0% {
        transform: scale(0.3);
    }
    80% {
        transform: scale(1.01);
    }
    100% {
        transform: scale(1);
    }
}
@keyframes bounce-out {
    0% {
        transform: scale(0.3);
    }
    20% {
        transform: scale(1.01);
    }
    100% {
        transform: scale(1);
    }
}
</style>
