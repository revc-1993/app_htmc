<script setup>
// import { useMainStore } from "@/stores/main";
import { computed } from "vue";
import UserAvatar from "@/components/UserAvatar.vue";
import { usePage } from "@inertiajs/vue3";

// const mainStore = useMainStore();

const userName = computed(
    () =>
        // $page.props.auth.user.name
        usePage().props.auth.name
);

// console.log(userName.value);
</script>

<template>
    <UserAvatar :username="userName.value" api="initials">
        <!-- <slot /> -->
    </UserAvatar>
</template>
