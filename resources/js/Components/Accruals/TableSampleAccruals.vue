<script setup>
import { computed, ref } from "vue";
import {
    mdiEye,
    mdiPenPlus,
    mdiTrashCan,
    mdiUpdate,
    mdiMagnify,
    mdiMenuUp,
    mdiMenuDown,
} from "@mdi/js";
import TableCheckboxCell from "@/Components/TableCheckboxCell.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import ExportButton from "@/Components/ExportButton.vue";
import SpanState from "@/components/SpanState.vue";
import CardBoxModalAccrual from "@/Components/Accruals/CardBoxModalAccrual.vue";
import CardBox from "@/Components/CardBox.vue";
import { usePage } from "@inertiajs/vue3";
import FormControl from "@/Components/FormControl.vue";
import BaseIcon from "@/components/BaseIcon.vue";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime"; // Importa el complemento de fechas relativas
import "dayjs/locale/es"; // Si deseas utilizar el idioma español

dayjs.locale("es");
dayjs.extend(relativeTime);

const props = defineProps({
    checkable: Boolean,
    accruals: Object,
    users: Object,
    roles: Object,
    recordStatuses: Object,
    instance: {
        type: String,
        default: "",
    },
});

// ---------------------------------------------------------
// CONSTANTES
// ---------------------------------------------------------
// Objeto Certificación
const accrual = ref({});
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
const items = computed(() => props.accruals);
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
const checked = (isChecked, accrual) => {
    if (isChecked) {
        checkedRows.value.push(accrual);
    } else {
        checkedRows.value = remove(
            checkedRows.value,
            (row) => row.id === accrual.id
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

const sortedAccruals = computed(() => {
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
const filteredAccruals = computed(() => {
    return sortedAccruals.value.filter((accrual) => {
        return (
            (accrual.commitment &&
                typeof accrual.commitment.commitment_cur === "number" &&
                accrual.commitment.commitment_cur
                    .toString()
                    .includes(searchTerm.value)) ||
            (accrual.commitment &&
                typeof accrual.commitment.process_number === "string" &&
                accrual.commitment.process_number
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (accrual.commitment?.vendor &&
                typeof accrual.commitment.vendor.name === "string" &&
                accrual.commitment.vendor.name
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (accrual.record_status &&
                accrual.record_status.status
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (typeof accrual.accrual_cur === "number" &&
                accrual.accrual_cur.toString().includes(searchTerm.value)) ||
            (typeof accrual.accrual_amount === "number" &&
                accrual.accrual_amount.toString().includes(searchTerm.value)) ||
            (accrual.current_management &&
                typeof accrual.current_management.name === "string" &&
                accrual.current_management.name
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (accrual.user &&
                accrual.user.username
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (accrual.payment.manager_status &&
                typeof accrual.payment.manager_status.status === "string" &&
                accrual.payment.manager_status.status
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
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
const openModal = (action, accruals = {}) => {
    accrual.value = accruals;
    currentOperation.value = action;
    isModalActive.value = true;
};

// --------------------------------------------
// CERRAR MODAL: CONFIRMAR O CANCELAR
// --------------------------------------------
const closeModal = (isconfirm) => {
    accrual.value = {};
    if (isconfirm === "confirm") alert(currentOperation.value);
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

const manageDate = (accrual) => {
    const startDate = {
        // 1: certification.sec_cgf_date,
        2: accrual.sec_cgf_date,
        3: accrual.assignment_date,
        4: accrual.accrual_date,
    }[accrual.current_management];

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
    <BaseLevel>
        <BaseButtons type="justify-start">
            <ExportButton
                :data="accruals"
                type="xls"
                color="success"
                :tooltip="'Exportar a Excel'"
            />
            <ExportButton
                :data="accruals"
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
                route-name="accruals.create"
                small
                v-show="hasPermissions('create')"
            />
        </BaseButtons>
    </BaseLevel>

    <CardBoxModalAccrual
        v-model="isModalActive"
        v-model:accrual="accrual"
        :users="users"
        :roles="roles"
        :record-statuses="recordStatuses"
        :element-props="Object.values(elementProps)[currentOperation - 1]"
        :current-operation="currentOperation"
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
                    <th v-if="checkable" class="text-center" />
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
                        @click="handleSort('commitment.commitment_cur')"
                        class="text-center"
                    >
                        N. Comp.
                        <span
                            v-if="orderBy === 'commitment.commitment_cur'"
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
                        @click="handleSort('commitment.process_number')"
                        class="text-center"
                    >
                        Proceso
                        <span
                            v-if="orderBy === 'commitment.process_number'"
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
                        @click="handleSort('commitment.vendor.name')"
                        class="text-center"
                    >
                        Proveedor
                        <span
                            v-if="orderBy === 'commitment.vendor.name'"
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
                    <th @click="handleSort('accrual_cur')" class="text-center">
                        CUR devengado
                        <span v-if="orderBy === 'accrual_cur'" class="ml-1">
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
                        @click="handleSort('accrual_amount')"
                        class="text-center"
                    >
                        Monto devengado
                        <span v-if="orderBy === 'accrual_amount'" class="ml-1">
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
                    <th
                        @click="handleSort('payment.manager_status.status')"
                        class="text-center"
                    >
                        Estado Tesorería
                        <span
                            v-if="orderBy === 'payment.manager_status.status'"
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
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="accrual in filteredAccruals"
                    :key="accrual.id"
                    class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <TableCheckboxCell
                        v-if="checkable"
                        @checked="checked($event, accrual)"
                    />
                    <td class="border-b-0 lg:w-6 before:hidden text-center">
                        {{ accrual.id }}
                        <!-- <UserAvatar
                        :username="certification.cf_contract_object"
                        class="w-24 h-24 mx-auto lg:w-6 lg:h-6"
                    /> -->
                    </td>
                    <td data-label="CUR Compromiso" class="text-center">
                        {{ accrual.commitment?.commitment_cur || "-" }}
                    </td>
                    <td data-label="N. Proceso" class="text-center uppercase">
                        {{ accrual.commitment?.process_number || "-" }}
                    </td>
                    <td data-label="Proveedor" class="text-center capitalize">
                        {{ accrual.commitment?.vendor.name || "-" }}
                    </td>
                    <td data-label="Estado" class="py-3 px-6 text-center">
                        <template v-if="accrual.record_status">
                            <SpanState :state="accrual.record_status" />
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td data-label="CUR devengado" class="text-center">
                        <strong>
                            {{ accrual.accrual_cur || "-" }}
                        </strong>
                    </td>
                    <td data-label="Monto devengado" class="text-center">
                        <strong>
                            {{
                                formattedNumber(accrual.accrual_amount) || "-"
                            }}</strong
                        >
                    </td>
                    <td
                        data-label="Gestión actual"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong>
                            {{ accrual.current_management.id }}.
                            {{ accrual.current_management.name }}
                        </strong>
                    </td>
                    <td
                        data-label="Asignado a"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong>
                            {{
                                accrual.user ? `@${accrual.user.username}` : "-"
                            }}
                        </strong>
                    </td>
                    <td
                        data-label="Tiempo"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400 text-xs text-bold"
                    >
                        {{ manageDate(accrual) }}
                    </td>
                    <td
                        data-label="Estado Tesorería"
                        class="py-3 px-6 text-center"
                    >
                        <template v-if="accrual.payment !== null">
                            <SpanState
                                :state="accrual.payment.manager_status"
                            />
                        </template>
                        <template v-else>-</template>
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
                                v-show="hasPermissions('show')"
                                @click="
                                    openModal(elementProps.show.tag, accrual)
                                "
                                small
                            />
                            <BaseButton
                                :color="elementProps.update.color"
                                :icon="elementProps.update.icon"
                                :tooltip="elementProps.update.tooltip"
                                small
                                v-show="hasPermissions('update')"
                                route-name="accruals.edit"
                                :id="accrual.id"
                            />
                            <BaseButton
                                :color="elementProps.delete.color"
                                :icon="elementProps.delete.icon"
                                :tooltip="elementProps.delete.tooltip"
                                small
                                v-show="hasPermissions('delete')"
                                @click="
                                    openModal(elementProps.delete.tag, accrual)
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
