<script setup>
import { computed, ref } from "vue";
import {
    mdiEye,
    mdiPenPlus,
    mdiMagnify,
    mdiTrashCan,
    mdiUpdate,
    mdiMenuDown,
    mdiMenuUp,
} from "@mdi/js";
import TableCheckboxCell from "@/Components/TableCheckboxCell.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import ExportButton from "@/Components/ExportButton.vue";
import SpanState from "@/components/SpanState.vue";
import CardBoxModalCertification from "@/Components/Certifications/CardBoxModalCertification.vue";
import CardBox from "@/components/CardBox.vue";
import FormControl from "@/components/FormControl.vue";
import { usePage } from "@inertiajs/vue3";
import BaseIcon from "@/components/BaseIcon.vue";

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
// Para ordenar por un campo
const orderBy = ref(null);
// Indica asc o desc
const orderDesc = ref(false);
// Término de búsqueda
const searchTerm = ref("");

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
// METODOS DE ORDENACION POR COLUMNA
// --------------------------------------------
const handleSort = (column) => {
    if (orderBy.value === column) {
        // Si la misma columna está siendo ordenada, cambia el tipo de orden
        orderDesc.value = !orderDesc.value;
    } else {
        // Si es una nueva columna, ordénala de forma ascendente
        orderBy.value = column;
        orderDesc.value = false;
    }
};

const sortedCertifications = computed(() => {
    if (orderBy.value) {
        return itemsPaginated.value.slice().sort((a, b) => {
            const columnA = getColumnValue(a, orderBy.value);
            const columnB = getColumnValue(b, orderBy.value);

            // Verifica si los valores son cadenas antes de comparar
            if (typeof columnA === "string" && typeof columnB === "string") {
                return orderDesc.value
                    ? columnB.localeCompare(columnA)
                    : columnA.localeCompare(columnB);
            } else {
                // Si no son cadenas, compara los valores directamente
                return orderDesc.value ? columnB - columnA : columnA - columnB;
            }
        });
    } else {
        return itemsPaginated.value;
    }
});

// Función para obtener el valor de una columna específica
const getColumnValue = (item, column) => {
    const columns = column.split("."); // Divide la cadena por puntos para manejar propiedades anidadas
    let value = item;
    for (const col of columns) {
        if (value && value[col] !== undefined && value[col] !== null) {
            value = value[col];
        } else {
            return ""; // Devuelve un valor predeterminado (cadena vacía) si la propiedad es nula o indefinida
        }
    }
    return value;
};

// --------------------------------------------
// FILTRAR
// --------------------------------------------
const filteredCertifications = computed(() => {
    return sortedCertifications.value.filter((certification) => {
        return (
            (certification.contract_object &&
                certification.contract_object
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (certification.department &&
                certification.department.department
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (typeof certification.certification_number === "number" &&
                certification.certification_number
                    .toString()
                    .includes(searchTerm.value)) ||
            (certification.record_status &&
                certification.record_status.status
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (certification.user &&
                certification.user.username
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (certification.current_management &&
                typeof certification.current_management.name === "string" &&
                certification.current_management.name
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) || // Otras condiciones para las columnas relevantes
            false
        );
    });
});

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
    const permissions = usePage().props.auth.user.permissions;
    return permissions.find((element) => element === permission);
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
    return {
        // 1: certification.sec_cgf_date,
        2: certification.sec_cgf_date,
        3: certification.assignment_date,
        4: certification.cp_date,
    }[certification.current_management.step];
};

const handleTimeAgo = (date) => {
    return timeAgo(date);
};

const dateIcon = (certification) => {
    return {
        // 1: certification.sec_cgf_date,
        2: certification.sec_cgf_date,
        3: certification.assignment_date,
        4: certification.cp_date,
    }[certification.current_management.step];
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
                :tooltip="'Exportar a Excel'"
            />
            <ExportButton
                :data="certifications"
                type="csv"
                color="slate"
                :tooltip="'Exportar a CSV'"
            />
            <FormControl
                v-model="searchTerm"
                placeholder="Buscar"
                :icon="mdiMagnify"
                ctrl-k-focus
                small
                class="px-1 py-1 text-sm"
            />
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
                v-show="hasPermissions('create')"
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
                    <th @click="handleSort('id')" class="text-center">
                        N.
                        <span v-if="orderBy === 'id'" class="ml-1">
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th
                        @click="handleSort('contract_object')"
                        class="text-center"
                    >
                        Objeto de contrato
                        <span v-if="orderBy === 'contract_object'" class="ml-1">
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th
                        @click="handleSort('department.department')"
                        class="text-center"
                    >
                        Area requirente
                        <span
                            v-if="orderBy === 'department.department'"
                            class="ml-1"
                        >
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th
                        @click="handleSort('record_status.status')"
                        class="text-center"
                    >
                        Estado
                        <span
                            v-if="orderBy === 'record_status.status'"
                            class="ml-1"
                        >
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th
                        @click="handleSort('certification_number')"
                        class="text-center"
                    >
                        N. certif.
                        <span
                            v-if="orderBy === 'certification_number'"
                            class="ml-1"
                        >
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th
                        @click="handleSort('certified_amount')"
                        class="text-center"
                    >
                        Monto
                        <span
                            v-if="orderBy === 'certified_amount'"
                            class="ml-1"
                        >
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th @click="handleSort('balance')" class="text-center">
                        Saldo
                        <span v-if="orderBy === 'balance'" class="ml-1">
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th
                        @click="handleSort('current_management.step')"
                        class="text-center"
                    >
                        Gestión actual
                        <span
                            v-if="orderBy === 'current_management.step'"
                            class="ml-1"
                        >
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th
                        @click="handleSort('user.username')"
                        class="text-center"
                    >
                        Asignado
                        <span v-if="orderBy === 'user.username'" class="ml-1">
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th @click="handleSort('sec_cgf_date')" class="text-center">
                        Tiempo
                        <span v-if="orderBy === 'sec_cgf_date'" class="ml-1">
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="certification in filteredCertifications"
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
                    <td
                        data-label="Objeto de contrato"
                        class="text-left capitalize"
                    >
                        {{ certification.contract_object }}
                    </td>
                    <td
                        data-label="Area requirente"
                        class="text-left capitalize"
                    >
                        {{
                            certification.department
                                ? certification.department.department
                                : "-"
                        }}
                    </td>
                    <td data-label="Estado" class="py-3 px-6 text-center">
                        <template v-if="certification.record_status">
                            <SpanState :state="certification.record_status" />
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td data-label="Nro. Certif." class="text-center">
                        <strong>
                            {{ certification.certification_number || "-" }}
                        </strong>
                    </td>

                    <td data-label="Monto" class="text-center">
                        <strong>
                            {{
                                formattedNumber(
                                    certification.certified_amount
                                ) || "-"
                            }}</strong
                        >
                    </td>
                    <td data-label="Saldo" class="text-center">
                        <strong>
                            {{
                                formattedNumber(certification.balance) || "-"
                            }}</strong
                        >
                    </td>
                    <td
                        data-label="Gestión actual"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong>
                            {{ certification.current_management.id }}.
                            {{ certification.current_management.name }}
                        </strong>
                    </td>
                    <td
                        data-label="Asignado a"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong>
                            {{
                                certification.user
                                    ? `@${certification.user.username}`
                                    : "-"
                            }}
                        </strong>
                    </td>
                    <td
                        data-label="Tiempo"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400 text-xs text-bold"
                    >
                        <!-- <DateIcon
                            v-if="certification"
                            :certification="certification"
                        /> -->
                        {{ handleTimeAgo(manageDate(certification)) }}
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
                                v-show="hasPermissions('show')"
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
                                v-show="hasPermissions('update')"
                                route-name="certifications.edit"
                                :id="certification.id"
                            />
                            <BaseButton
                                :color="elementProps.delete.color"
                                :icon="elementProps.delete.icon"
                                :tooltip="elementProps.delete.tooltip"
                                small
                                v-show="hasPermissions('delete')"
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
