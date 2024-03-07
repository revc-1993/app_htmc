<script setup>
import exportFromJSON from "export-from-json";
import { computed, ref } from "vue";
import { mdiCommaBox, mdiFileExcelBox } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";

const props = defineProps({
    data: Object,
    type: String,
    color: String,
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

const exportIcon = computed(() => {
    return {
        csv: mdiCommaBox,
        xls: mdiFileExcelBox,
    }[props.type];
});
</script>
<template>
    <BaseButton
        small
        :color="color"
        :icon="exportIcon"
        :tooltip="tooltip"
        @click="downloadFile(type)"
    />
</template>
