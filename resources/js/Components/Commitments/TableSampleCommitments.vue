<script setup>
import { computed, ref } from "vue";
import {
    mdiMagnify,
    mdiEye,
    mdiPenPlus,
    mdiTrashCan,
    mdiUpdate,
    mdiMenuUp,
    mdiMenuDown,
} from "@mdi/js";
import TableCheckboxCell from "@/Components/TableCheckboxCell.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import ExportButton from "@/Components/ExportButton.vue";
import SpanState from "@/components/SpanState.vue";
import CardBoxModalCommitment from "@/Components/Commitments/CardBoxModalCommitment.vue";
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
    commitments: Object,
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
// Objeto Compromiso
const commitment = ref({});
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
const items = computed(() => props.commitments);
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
const checked = (isChecked, commitment) => {
    if (isChecked) {
        checkedRows.value.push(commitment);
    } else {
        checkedRows.value = remove(
            checkedRows.value,
            (row) => row.id === commitment.id
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

const sortedCommitments = computed(() => {
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
const filteredCommitments = computed(() => {
    return sortedCommitments.value.filter((commitment) => {
        return (
            (commitment.certification &&
                typeof commitment.certification.certification_number ===
                    "number" &&
                commitment.certification.certification_number
                    .toString()
                    .includes(searchTerm.value)) ||
            (commitment.process_number &&
                commitment.process_number
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (commitment.contract_administrator &&
                commitment.contract_administrator.names
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (commitment.vendor &&
                typeof commitment.vendor.name === "string" &&
                commitment.vendor.name
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (commitment.record_status &&
                commitment.record_status.status
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (typeof commitment.commitment_cur === "number" &&
                commitment.commitment_cur
                    .toString()
                    .includes(searchTerm.value)) ||
            (commitment.commitment_amount &&
                typeof commitment.commitment_amount === "number" &&
                commitment.commitment_amount
                    .toString()
                    .includes(searchTerm.value)) ||
            (commitment.current_management &&
                typeof commitment.current_management.name === "string" &&
                commitment.current_management.name
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (commitment.user &&
                commitment.user.username
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
const openModal = (action, commitments = {}) => {
    commitment.value = commitments;
    currentOperation.value = action;
    isModalActive.value = true;
};

// --------------------------------------------
// CERRAR MODAL: CONFIRMAR O CANCELAR
// --------------------------------------------
const closeModal = (isconfirm) => {
    commitment.value = {};
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

const manageDate = (commitment) => {
    const startDate = {
        // 1: certification.sec_cgf_date,
        2: commitment.sec_cgf_date,
        3: commitment.assignment_date,
        4: commitment.cp_date,
    }[commitment.current_management];

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
                :data="commitments"
                type="xls"
                color="success"
                :tooltip="'Exportar a Excel'"
            />
            <ExportButton
                :data="commitments"
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
                route-name="commitments.create"
                small
                v-show="hasPermissions('create')"
            />
        </BaseButtons>
    </BaseLevel>

    <CardBoxModalCommitment
        v-model="isModalActive"
        v-model:commitment="commitment"
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
                        @click="
                            handleSort('certification.certification_number')
                        "
                        class="text-center"
                    >
                        N. Certif.
                        <span
                            v-if="
                                orderBy === 'certification.certification_number'
                            "
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
                        @click="handleSort('process_number')"
                        class="text-center"
                    >
                        Proceso
                        <span v-if="orderBy === 'process_number'" class="ml-1">
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
                        @click="handleSort('contract_administrator.names')"
                        class="text-center"
                    >
                        Administrador de contrato
                        <span
                            v-if="orderBy === 'contract_administrator.names'"
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
                    <th @click="handleSort('vendor.name')" class="text-center">
                        Proveedor
                        <span v-if="orderBy === 'vendor.name'" class="ml-1">
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
                        @click="handleSort('commitment_cur')"
                        class="text-center"
                    >
                        N. Compr.
                        <span v-if="orderBy === 'commitment_cur'" class="ml-1">
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
                        @click="handleSort('commitment_amount')"
                        class="text-center"
                    >
                        Monto comp.
                        <span
                            v-if="orderBy === 'commitment_amount'"
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
                    v-for="commitment in filteredCommitments"
                    :key="commitment.id"
                    class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <TableCheckboxCell
                        v-if="checkable"
                        @checked="checked($event, commitment)"
                    />
                    <td class="border-b-0 lg:w-6 before:hidden text-center">
                        {{ commitment.id }}
                        <!-- <UserAvatar
                        :username="certification.cf_contract_object"
                        class="w-24 h-24 mx-auto lg:w-6 lg:h-6"
                    /> -->
                    </td>
                    <td data-label="N. Certif." class="text-center">
                        {{
                            commitment.certification
                                ? commitment.certification.certification_number
                                : "-"
                        }}
                    </td>
                    <td data-label="N. Proceso" class="text-center uppercase">
                        {{ commitment.process_number || "-" }}
                    </td>
                    <td
                        data-label="Administrador de contrato"
                        class="text-center capitalize"
                    >
                        {{ commitment.contract_administrator.names }}
                    </td>
                    <td data-label="Proveedor" class="text-center capitalize">
                        {{ commitment.vendor ? commitment.vendor.name : "-" }}
                    </td>
                    <td data-label="Estado" class="py-3 px-6 text-center">
                        <template v-if="commitment.record_status">
                            <SpanState :state="commitment.record_status" />
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td data-label="Nro. Comp." class="text-center">
                        <strong>
                            {{ commitment.commitment_cur || "-" }}
                        </strong>
                    </td>
                    <td data-label="Monto" class="text-center">
                        <strong>
                            {{
                                formattedNumber(commitment.commitment_amount) ||
                                "-"
                            }}</strong
                        >
                    </td>
                    <td data-label="Saldo" class="text-center">
                        <strong>
                            {{
                                formattedNumber(commitment.balance) || "-"
                            }}</strong
                        >
                    </td>

                    <td
                        data-label="Gestión actual"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong>
                            {{ commitment.current_management.id }}.
                            {{ commitment.current_management.name }}
                        </strong>
                    </td>
                    <td
                        data-label="Asignado a"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong>
                            {{
                                commitment.user
                                    ? `@${commitment.user.username}`
                                    : "-"
                            }}
                        </strong>
                    </td>
                    <td
                        data-label="Tiempo"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400 text-xs text-bold"
                    >
                        {{ manageDate(commitment) }}
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
                                    openModal(elementProps.show.tag, commitment)
                                "
                                small
                            />
                            <BaseButton
                                :color="elementProps.update.color"
                                :icon="elementProps.update.icon"
                                :tooltip="elementProps.update.tooltip"
                                small
                                v-show="hasPermissions('update')"
                                route-name="commitments.edit"
                                :id="commitment.id"
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
                                        commitment
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
