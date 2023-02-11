<script setup>
const props = defineProps({
    id: String,
    label: String,
});
</script>

<template>
    <div
        :id="id"
        role="tooltip"
        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 tooltip"
    >
        {{ label }}
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>
</template>
