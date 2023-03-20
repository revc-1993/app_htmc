<script setup>
import exportFromJSON from "export-from-json";
import { computed, ref } from "vue";
import BaseButton from "@/components/BaseButton.vue";

const props = defineProps({
    data: Object,
    type: String,
    color: String,
    icon: String,
    tooltip: String,
});

const data = props.data;

const downloadFile = (type) => {
    const fileName = "export";
    const exportType = {
        xls: exportFromJSON.types.xls,
        csv: exportFromJSON.types.csv,
    }[type];

    if (data) exportFromJSON({ data, fileName, exportType });
};
</script>
<template>
    <BaseButton
        small
        :color="color"
        :icon="icon"
        :tooltip="tooltip"
        @click="downloadFile(type)"
    />
</template>
