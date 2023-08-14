<script setup>
const props = defineProps({
    navBar: Boolean,
});
</script>

<template>
    <hr
        :class="
            props.navBar
                ? 'hidden lg:block lg:my-0.5 dark:border-slate-700'
                : 'my-6 -mx-3 dark:border-slate-800'
        "
        class="my-6 -mx-3 border-t border-gray-100"
    />
</template>
