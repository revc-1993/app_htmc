<script setup>
import { computed, ref } from "vue";
import PillTag from "@/components/PillTag.vue";
import {
    mdiPenPlus,
    mdiTrashCan,
    mdiUpdate,
    mdiEye,
    mdiFileDelimited,
    mdiFileExcel,
    mdiMagnify,
    mdiMenuUp,
    mdiMenuDown,
} from "@mdi/js";
import TableCheckboxCell from "@/Components/TableCheckboxCell.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import ExportButton from "@/Components/ExportButton.vue";
import CardBoxModalUser from "@/Components/Users/CardBoxModalUser.vue";
import CardBox from "@/components/CardBox.vue";
import FormControl from "@/components/FormControl.vue";
import { usePage } from "@inertiajs/vue3";
import BaseIcon from "@/components/BaseIcon.vue";
import { useStyleStore } from "@/Stores/style.js";

const props = defineProps({
    checkable: Boolean,
    users: Object,
    instance: {
        type: String,
        default: "",
    },
});

const styleStore = useStyleStore();

// ---------------------------------------------------------
// CONSTANTES
// ---------------------------------------------------------
// Objeto Usuario
const user = ref({});
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
const items = computed(() => props.users);
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
const checked = (isChecked, user) => {
    if (isChecked) {
        checkedRows.value.push(user);
    } else {
        checkedRows.value = remove(
            checkedRows.value,
            (row) => row.id === user.id
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

const sortedUsers = computed(() => {
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
const filteredUsers = computed(() => {
    return sortedUsers.value.filter((user) => {
        return (
            (user.name &&
                user.name
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (user.username &&
                user.username
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            (user.email &&
                user.email
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
const openModal = (action, users = {}) => {
    user.value = users;
    currentOperation.value = action;
    isModalActive.value = true;
};

// --------------------------------------------
// CERRAR MODAL: CONFIRMAR O CANCELAR
// --------------------------------------------
const closeModal = (event) => {
    user.value = {};
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
</script>

<template>
    <BaseLevel>
        <BaseButtons type="justify-start">
            <ExportButton
                :data="users"
                type="xls"
                color="success"
                :icon="mdiFileExcel"
                :tooltip="'Exportar a Excel'"
            />
            <ExportButton
                :data="users"
                type="csv"
                color="slate"
                :icon="mdiFileDelimited"
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
                route-name="superadmin.users.create"
                small
                v-show="hasPermissions('create')"
            />
        </BaseButtons>
    </BaseLevel>

    <CardBoxModalUser
        v-model="isModalActive"
        v-model:user="user"
        :current-operation="currentOperation"
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
                    <th @click="handleSort('name')" class="text-center">
                        Nombres
                        <span v-if="orderBy === 'name'" class="ml-1">
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th @click="handleSort('username')" class="text-center">
                        Username
                        <span v-if="orderBy === 'username'" class="ml-1">
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <th @click="handleSort('email')" class="text-center">
                        Correo electrónico
                        <span v-if="orderBy === 'email'" class="ml-1">
                            <BaseIcon
                                :path="orderDesc ? mdiMenuUp : mdiMenuDown"
                                h="h-4"
                                w="w-4"
                                class="mr-1"
                                :size="14"
                            />
                        </span>
                    </th>
                    <!-- <th class="text-center">Departamento</th> -->
                    <th class="text-center">Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="user in filteredUsers"
                    :key="user.id"
                    class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <TableCheckboxCell
                        v-if="checkable"
                        @checked="checked($event, user)"
                    />
                    <td class="border-b-0 lg:w-6 before:hidden text-center">
                        {{ user.id }}
                        <!-- <UserAvatar
                        :username="certification.cf_contract_object"
                        class="w-24 h-24 mx-auto lg:w-6 lg:h-6"
                    /> -->
                    </td>
                    <td data-label="Nombres" class="text-center">
                        {{ user.name }}
                    </td>
                    <td data-label="Username" class="text-center">
                        {{ user.username }}
                    </td>
                    <td data-label="Correo electrónico" class="text-center">
                        {{ user.email }}
                    </td>
                    <!-- <td data-label="Departamento" class="text-center">
                        {{ user.department }}
                    </td> -->
                    <td data-label="Rol" class="py-3 px-6 text-center">
                        <!-- <SpanState
                            v-if="user.record_status"
                            :state="commitment.record_status"
                        />
                        <div v-else>-</div> -->
                        <PillTag
                            color="teal"
                            :label="user.roles[0].name"
                            :small="true"
                            :outline="styleStore.darkMode"
                        />
                    </td>
                    <td
                        class="before:hidden lg:w-1 whitespace-nowrap text-center"
                    >
                        <BaseButtons
                            type="justify-start lg:justify-center whitespace-nowrap"
                            no-wrap
                        >
                            <BaseButton
                                :color="elementProps.update.color"
                                :icon="elementProps.update.icon"
                                :tooltip="elementProps.update.tooltip"
                                small
                                v-show="hasPermissions('update')"
                                route-name="superadmin.users.edit"
                                :id="user.id"
                            />
                            <BaseButton
                                :color="elementProps.delete.color"
                                :icon="elementProps.delete.icon"
                                :tooltip="elementProps.delete.tooltip"
                                small
                                v-show="hasPermissions('delete')"
                                @click="
                                    openModal(elementProps.delete.tag, user)
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
