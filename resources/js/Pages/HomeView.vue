<script setup>
import { Head } from "@inertiajs/vue3";

import { computed, ref, onMounted } from "vue";
import { useMainStore } from "@/stores/main";
import {
    mdiTextBoxMultipleOutline,
    mdiRefresh,
    mdiCash,
    mdiAccountMultiple,
    mdiChartTimelineVariant,
    mdiMonitorCellphone,
    mdiReload,
    mdiFileDocumentEditOutline,
    mdiCardBulleted,
    mdiChartPie,
} from "@mdi/js";
import * as chartConfig from "@/Components/Charts/chart.config.js";
import LineChart from "@/Components/Charts/LineChart.vue";
import SectionMain from "@/Components/SectionMain.vue";
import CardBoxWidget from "@/Components/CardBoxWidget.vue";
import CardBox from "@/Components/CardBox.vue";
import TableSampleClients from "@/Components/TableSampleClients.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBoxTransaction from "@/Components/CardBoxTransaction.vue";
import CardBoxClient from "@/Components/CardBoxClient.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import SectionBannerStarOnGitHub from "@/Components/SectionBannerStarOnGitHub.vue";
import Toast from "@/Components/Toast.vue";

const props = defineProps({
    certifications_percent: Number,
    n_certifications_ok: Number,
    commitments_percent: Number,
    n_commitments_ok: Number,
    pending_certifications: Object,
    certifications_amount_ordered: Object,
});
const chartData = ref(null);

const fillChartData = () => {
    chartData.value = chartConfig.sampleChartData();
};

onMounted(() => {
    fillChartData();
});

const mainStore = useMainStore();

const clientBarItems = computed(() => mainStore.clients.slice(0, 4));

const transactionBarItems = computed(() => mainStore.history);
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Dashboard" />
        <SectionMain>
            <Toast
                v-if="$page.props.flash.message"
                :message="$page.props.flash.message"
            />

            <!-- <SectionTitleLineWithButton
                :icon="mdiChartTimelineVariant"
                title="Estadísticas"
                main
            />

            <div
                class="grid grid-cols-1 gap-6 md:grid-cols-3 lg:grid-cols-5 mb-6"
            >
                <CardBoxWidget
                    :trend="certifications_percent + '%'"
                    :trend-type="certifications_percent > 50 ? 'up' : 'down'"
                    color="text-gray-500"
                    :icon="mdiCardBulleted"
                    :number="n_certifications_ok"
                    label="Certificaciones"
                    :is-button="false"
                />
                <CardBoxWidget
                    :trend="commitments_percent + '%'"
                    :trend-type="commitments_percent > 50 ? 'up' : 'down'"
                    color="text-orange-400"
                    :icon="mdiTextBoxMultipleOutline"
                    :number="n_commitments_ok"
                    label="Compromisos"
                    :is-button="false"
                />
                <CardBoxWidget
                    trend="Overflow"
                    trend-type="alert"
                    color="text-cyan-500"
                    :icon="mdiFileDocumentEditOutline"
                    :number="31"
                    label="Devengos"
                    :is-button="false"
                />
                <CardBoxWidget
                    trend="12%"
                    trend-type="up"
                    color="text-slate-500"
                    :icon="mdiRefresh"
                    :number="13"
                    label="Anticipos"
                    :is-button="false"
                />
                <CardBoxWidget
                    trend="12%"
                    trend-type="down"
                    color="text-emerald-500"
                    :icon="mdiCash"
                    :number="24"
                    label="Pagos"
                    :is-button="false"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="flex flex-col justify-between">
                    <CardBoxTransaction
                        v-for="(certification, index) in pending_certifications"
                        :key="index"
                        :amount="certification.contract_object"
                        :date="certification.reception_date"
                        :business="certification.department.department"
                        :type="certification.management_status"
                        :name="certification.user.name"
                        :account="certification.user.name"
                    />
                </div>
                <div class="flex flex-col justify-between">
                    <CardBoxTransaction
                        v-for="(
                            certification, index
                        ) in certifications_amount_ordered"
                        :key="index"
                        :amount="certification.contract_object"
                        :date="certification.reception_date"
                        :business="certification.department.department"
                        :type="certification.management_status"
                        :name="'$ ' + certification.amount"
                        :account="certification.user.name"
                    />
                </div>
            </div> -->

            <!-- <SectionBannerStarOnGitHub class="mt-6 mb-6" /> -->

            <!-- <SectionTitleLineWithButton
                :icon="mdiChartPie"
                title="Trends overview"
            >
                <BaseButton
                    :icon="mdiReload"
                    color="whiteDark"
                    @click="fillChartData"
                />
            </SectionTitleLineWithButton>

            <CardBox class="mb-6">
                <div v-if="chartData">
                    <line-chart :data="chartData" class="h-96" />
                </div>
            </CardBox>

            <SectionTitleLineWithButton
                :icon="mdiAccountMultiple"
                title="Clients"
            />

            <NotificationBar color="info" :icon="mdiMonitorCellphone">
                <b>Responsive table.</b> Collapses on mobile
            </NotificationBar> -->

            <!-- <CardBox has-table>
                <TableSampleClients />
            </CardBox> -->
        </SectionMain>
    </LayoutAuthenticated>
</template>

<style>
.Vue-Toastification__toast--default.height-class {
    height: max-content;
}
.Vue-Toastification__toast--default.danger-class {
    background-color: rgb(220 38 38);
}
.Vue-Toastification__toast--default.warning-class {
    background-color: rgb(202 138 4);
}
.Vue-Toastification__toast--default.success-class {
    background-color: rgb(5 150 105);
}
.Vue-Toastification__toast--default.blue-class {
    background-color: rgb(37 99 235);
}

.Vue-Toastification__toast-body.custom-class {
    font-size: 0.875rem; /* 14px */
    line-height: 1.25rem; /* 20px */
}
</style>
