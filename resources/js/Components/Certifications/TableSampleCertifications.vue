<script setup>
import { computed, ref } from "vue";
import {
    mdiEye,
    mdiPenPlus,
    mdiTrashCan,
    mdiFileExcel,
    mdiFileDelimited,
    mdiUpdate,
} from "@mdi/js";
import TableCheckboxCell from "@/Components/TableCheckboxCell.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import ExportButton from "@/Components/ExportButton.vue";
import SpanState from "@/components/SpanState.vue";
import CardBoxModalCertification from "@/Components/Certifications/CardBoxModalCertification.vue";
import CardBox from "@/Components/CardBox.vue";
import { usePage } from "@inertiajs/vue3";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime"; // Importa el complemento de fechas relativas
import "dayjs/locale/es"; // Si deseas utilizar el idioma español

dayjs.locale("es");
dayjs.extend(relativeTime);

const props = defineProps({
    checkable: Boolean,
    certifications: Object,
    instance: {
        type: String,
        default: "",
    },
    departments: Object,
    processTypes: Object,
    expenseTypes: Object,
    budgetLines: Object,
    users: Object,
    roles: Object,
    recordStatuses: Object,
});

// ---------------------------------------------------------
// CONSTANTES
// ---------------------------------------------------------
// Objeto Certificación
const certification = ref({});
// Modal abierto o cerrado
const isModalActive = ref(false);
// Operación escogida para modal
const currentOperation = ref("");

// Características de botones por operación
const elementProps = {
    create: {
        tag: 1,
        operation: "create",
        color: "success",
        label: "Crear " + props.instance,
        tooltip: "Crear " + props.instance,
        icon: mdiPenPlus,
    },
    show: {
        tag: 2,
        operation: "show",
        color: "info",
        label: "Ver " + props.instance,
        tooltip: "Ver detalles",
        icon: mdiEye,
    },
    update: {
        tag: 3,
        operation: "update",
        color: "warning",
        label: "Actualizar " + props.instance,
        tooltip: "Actualizar",
        icon: mdiUpdate,
    },
    delete: {
        tag: 4,
        operation: "delete",
        color: "danger",
        label: "Eliminar " + props.instance,
        tooltip: "Eliminar " + props.instance,
        icon: mdiTrashCan,
    },
};

// ---------------------------------------------------------
// TABLA: PAGINACIÓN
// ---------------------------------------------------------
const items = computed(() => props.certifications);
const perPage = ref(5);
const currentPage = ref(0);
const itemsPaginated = computed(() =>
    items.value.slice(
        perPage.value * currentPage.value,
        perPage.value * (currentPage.value + 1)
    )
);
const numPages = Math.ceil(Object.keys(items.value).length / perPage.value);
const currentPageHuman = computed(() => currentPage.value + 1);
const pagesList = computed(() => {
    const pagesList = [];
    for (let i = 0; i < numPages; i++) {
        pagesList.push(i);
    }
    return pagesList;
});

// ---------------------------------------------------------
// OPCIÓN CHECKEABLE
// ---------------------------------------------------------
const checkedRows = ref([]);
const remove = (arr, cb) => {
    const newArr = [];
    arr.forEach((item) => {
        if (!cb(item)) {
            newArr.push(item);
        }
    });
    return newArr;
};
const checked = (isChecked, certification) => {
    if (isChecked) {
        checkedRows.value.push(certification);
    } else {
        checkedRows.value = remove(
            checkedRows.value,
            (row) => row.id === certification.id
        );
    }
};

// --------------------------------------------
// EMIT DE ALERTA
// --------------------------------------------
const emit = defineEmits(["alert"]);
const alerts = (mode, operation) => {
    emit(mode, operation);
};
const alert = (operation) => alerts("alert", operation);

// --------------------------------------------
// ABRIR MODAL: 1 Create, 2 Show, 3 Update, 4 Delete
// --------------------------------------------
const openModal = (action, certifications = {}) => {
    certification.value = certifications;
    currentOperation.value = action;
    isModalActive.value = true;
};

// --------------------------------------------
// CERRAR MODAL: CONFIRMAR O CANCELAR
// --------------------------------------------
const closeModal = (event) => {
    certification.value = {};
    if (event === "confirm") alert(currentOperation.value);
    currentOperation.value = "";
    isModalActive.value = false;
};

// --------------------------------------------
// PERMISOS
// --------------------------------------------
const hasPermissions = (permission) => {
    return usePage().props.user.permissions.includes(permission);
};

// --------------------------------------------
// FECHAS
// --------------------------------------------
const timeAgo = (date) => {
    return dayjs(date).fromNow();
};

const daysDifference = (startDate, endDate) => {
    const start = dayjs(startDate);
    const end = dayjs(endDate);
    const differenceInDays = end.from(start, true); // Calcula la diferencia en días
    return `Hace ${differenceInDays}`;
};

const manageDate = (certification) => {
    const startDate = {
        // 1: certification.sec_cgf_date,
        2: certification.sec_cgf_date,
        3: certification.assignment_date,
        4: certification.cp_date,
    }[certification.current_management];

    return timeAgo(startDate);
};

// --------------------------------------------
// FORMATEO DE MONEDA
// --------------------------------------------
const formattedNumber = (number) => {
    const f = new Intl.NumberFormat("eng-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    return f.format(number);
};
</script>

<template>
    <!-- BOTON CREATE (OK) -->
    <BaseLevel>
        <BaseButtons type="justify-start">
            <ExportButton
                :data="certifications"
                type="xls"
                color="success"
                :icon="mdiFileExcel"
                :tooltip="'Exportar a Excel'"
            />
            <ExportButton
                :data="certifications"
                type="csv"
                color="slate"
                :icon="mdiFileDelimited"
                :tooltip="'Exportar a CSV'"
            />
            <!-- <FormControl
                placeholder="Buscar"
                :icon="mdiMagnify"
                ctrl-k-focus
                class="px-1 py-1 text-sm"
            /> -->
        </BaseButtons>
        <BaseButtons type="justify-end">
            <!-- BUTTON HACIA OTRA PAGINA -->
            <BaseButton
                :color="elementProps.create.color"
                :icon="elementProps.create.icon"
                :label="elementProps.create.label"
                :tooltip="elementProps.create.tooltip"
                route-name="certifications.create"
                small
                v-show="hasPermissions('create_certification')"
            />
        </BaseButtons>
    </BaseLevel>

    <CardBoxModalCertification
        v-model="isModalActive"
        v-model:certification="certification"
        :departments="departments"
        :process-types="processTypes"
        :expense-types="expenseTypes"
        :budget-lines="budgetLines"
        :current-operation="currentOperation"
        :users="users"
        :roles="roles"
        :record-statuses="recordStatuses"
        :element-props="Object.values(elementProps)[currentOperation - 1]"
        @confirm="closeModal"
    />

    <CardBox class="mb-6" has-table>
        <template v-show="checkedRows.length">
            <div class="p-3 bg-gray-100/50 dark:bg-slate-800">
                <span
                    v-for="checkedRow in checkedRows"
                    :key="checkedRow.id"
                    class="inline-block px-2 py-1 rounded-sm mr-2 text-sm bg-gray-100 dark:bg-slate-700"
                >
                    {{ checkedRow.name }}
                </span>
            </div>
        </template>

        <table class="table">
            <thead>
                <tr>
                    <!-- class="bg-gray-300 text-gray-600 uppercase text-sm leading-normal" -->
                    <th v-show="checkable" class="text-center" />
                    <th class="text-center">N.</th>
                    <th class="text-center">Objeto de contrato</th>
                    <th class="text-center">Area requirente</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">N. certif.</th>
                    <th class="text-center">Monto</th>
                    <th class="text-center">Saldo</th>
                    <th class="text-center">Gestión actual</th>
                    <th class="text-center">Asignado</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="certification in itemsPaginated"
                    :key="certification.id"
                    class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <TableCheckboxCell
                        v-show="checkable"
                        @checked="checked($event, certification)"
                    />
                    <td class="border-b-0 lg:w-6 before:hidden text-center">
                        {{ certification.id }}
                        <!-- <UserAvatar
                        :username="certification.cf_contract_object"
                        class="w-24 h-24 mx-auto lg:w-6 lg:h-6"
                    /> -->
                    </td>
                    <td data-label="Objeto de contrato" class="text-left">
                        {{ certification.contract_object }}
                    </td>
                    <td data-label="Area requirente" class="text-left">
                        {{ certification.department.department }}
                    </td>
                    <td data-label="Estado" class="py-3 px-6 text-center">
                        <template v-if="certification.record_status">
                            <SpanState :state="certification.record_status" />
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td data-label="Nro. Certif." class="text-center">
                        <template v-if="certification.certification_number">
                            <strong>
                                {{ certification.certification_number }}
                            </strong>
                        </template>
                        <template v-else>-</template>
                    </td>

                    <td data-label="Monto" class="text-center">
                        <template v-if="certification.certified_amount">
                            <strong>
                                {{
                                    formattedNumber(
                                        certification.certified_amount
                                    )
                                }}</strong
                            >
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td data-label="Saldo" class="text-center">
                        <template v-if="certification.balance">
                            <strong>
                                {{
                                    formattedNumber(certification.balance)
                                }}</strong
                            >
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td
                        data-label="Gestión actual"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong>
                            {{
                                roles[certification.current_management - 1].id
                            }}.
                            {{
                                roles[certification.current_management - 1]
                                    .nickname
                            }}
                        </strong>
                    </td>
                    <td
                        data-label="Asignado a"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <template v-if="certification.user">
                            <strong>
                                @{{ certification.user.username }}
                            </strong>
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td
                        data-label="Tiempo"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400 text-xs text-bold"
                    >
                        {{ manageDate(certification) }}
                    </td>
                    <td
                        class="before:hidden lg:w-1 whitespace-nowrap text-center"
                    >
                        <BaseButtons
                            type="justify-start lg:justify-center whitespace-nowrap"
                            no-wrap
                        >
                            <BaseButton
                                :color="elementProps.show.color"
                                :icon="elementProps.show.icon"
                                :tooltip="elementProps.show.tooltip"
                                small
                                v-show="hasPermissions('show_certification')"
                                @click="
                                    openModal(
                                        elementProps.show.tag,
                                        certification
                                    )
                                "
                            />
                            <BaseButton
                                :color="elementProps.update.color"
                                :icon="elementProps.update.icon"
                                :tooltip="elementProps.update.tooltip"
                                small
                                v-show="hasPermissions('update_certification')"
                                route-name="certifications.edit"
                                :id="certification.id"
                            />
                            <BaseButton
                                :color="elementProps.delete.color"
                                :icon="elementProps.delete.icon"
                                :tooltip="elementProps.delete.tooltip"
                                small
                                v-show="hasPermissions('delete_certification')"
                                @click="
                                    openModal(
                                        elementProps.delete.tag,
                                        certification
                                    )
                                "
                            />
                        </BaseButtons>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
            <BaseLevel>
                <BaseButtons>
                    <BaseButton
                        v-for="(page, key) in pagesList"
                        :key="key"
                        :active="page === currentPage"
                        :label="page + 1"
                        :color="
                            page === currentPage ? 'lightDark' : 'whiteDark'
                        "
                        small
                        @click="currentPage = page"
                    />
                </BaseButtons>
                <small
                    >Página {{ numPages > 0 ? currentPageHuman : 0 }} de
                    {{ numPages }}</small
                >
            </BaseLevel>
        </div>
    </CardBox>
</template>
