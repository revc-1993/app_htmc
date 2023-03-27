<script setup>
import { computed, ref } from "vue";
import {
    mdiAccountSearch,
    mdiAccountEdit,
    mdiAccountRemove,
    mdiAccountPlus,
    mdiFileDelimited,
    mdiFileExcel,
} from "@mdi/js";
import TableCheckboxCell from "@/components/TableCheckboxCell.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import BaseButton from "@/components/BaseButton.vue";
import ExportButton from "@/components/ExportButton.vue";
import SpanState from "@/components/SpanState.vue";
// import CardBoxModalCommitment from "@/components/commitments/CardBoxModalCommitment.vue";
import CardBox from "@/components/CardBox.vue";

const props = defineProps({
    checkable: Boolean,
    users: Object,
    instance: {
        type: String,
        default: "",
    },
});

// ---------------------------------------------------------
// CONSTANTES
// ---------------------------------------------------------
// Objeto Usuario
const user = ref({});
// Modal abierto o cerrado
const isModalActive = ref(false);
// Operación escogida para modal
const currentOperation = ref("");
// Operaciones
const operations = {
    1: "create",
    2: "show",
    3: "update",
    4: "delete",
};

// Características de botones por operación
const elementProps = {
    create: {
        tag: 1,
        color: "success",
        label: "Crear " + props.instance,
        tooltip: "Crear " + props.instance,
        icon: mdiAccountPlus,
    },
    show: {
        tag: 2,
        color: "info",
        label: "Ver " + props.instance,
        tooltip: "Ver detalles",
        icon: mdiAccountSearch,
    },
    update: {
        tag: 3,
        color: "warning",
        label: "Actualizar " + props.instance,
        tooltip: "Actualizar",
        icon: mdiAccountEdit,
    },
    delete: {
        tag: 4,
        color: "danger",
        label: "Eliminar " + props.instance,
        tooltip: "Eliminar",
        icon: mdiAccountRemove,
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
const closeModal = (isconfirm) => {
    if (isconfirm === "confirm") alert(currentOperation.value);
    currentOperation.value = "";
    isModalActive.value = false;
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
                route-name="users.create"
                small
                v-if="$page.props.user.permissions.includes('create_user')"
            />
        </BaseButtons>
    </BaseLevel>

    <!-- <CardBoxModalCommitment
        v-model="isModalActive"
        v-model:user="user"
        :element-props="elementProps[operations[currentOperation]]"
        :current-operation="currentOperation"
        @confirm="closeModal"
    /> -->

    <CardBox class="mb-6" has-table>
        <div
            v-if="checkedRows.length"
            class="p-3 bg-gray-100/50 dark:bg-slate-800"
        >
            <span
                v-for="checkedRow in checkedRows"
                :key="checkedRow.id"
                class="inline-block px-2 py-1 rounded-sm mr-2 text-sm bg-gray-100 dark:bg-slate-700"
            >
                {{ checkedRow.name }}
            </span>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <!-- class="bg-gray-300 text-gray-600 uppercase text-sm leading-normal" -->
                    <th v-if="checkable" class="text-center" />
                    <th class="text-center">N.</th>
                    <th class="text-center">Nombres</th>
                    <th class="text-center">Correo electrónico</th>
                    <th class="text-center">Departamento</th>
                    <th class="text-center">Nick</th>
                    <th class="text-center">Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="user in itemsPaginated"
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
                    <td data-label="Correo electrónico" class="text-center">
                        {{ user.email }}
                    </td>
                    <td data-label="Departamento" class="text-center">
                        {{ user.department }}
                    </td>
                    <td
                        data-label="Nick"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong v-if="user.username">
                            @{{ user.username }}
                        </strong>
                        <div v-else>-</div>
                    </td>
                    <td data-label="Rol" class="py-3 px-6 text-center">
                        <!-- <SpanState
                            v-if="user.record_status"
                            :state="commitment.record_status"
                        />
                        <div v-else>-</div> -->
                        {{ user.roles[0].name }}
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
                                v-if="
                                    $page.props.user.permissions.includes(
                                        'show_user'
                                    )
                                "
                                @click="openModal(elementProps.show.tag, user)"
                                small
                            />
                            <BaseButton
                                :color="elementProps.update.color"
                                :icon="elementProps.update.icon"
                                :tooltip="elementProps.update.tooltip"
                                v-if="
                                    $page.props.user.permissions.includes(
                                        'update_user'
                                    )
                                "
                                route-name="users.edit"
                                :id="user.id"
                                small
                            />
                            <BaseButton
                                :color="elementProps.delete.color"
                                :icon="elementProps.delete.icon"
                                :tooltip="elementProps.delete.tooltip"
                                v-if="
                                    $page.props.user.permissions.includes(
                                        'delete_user'
                                    )
                                "
                                @click="
                                    openModal(elementProps.delete.tag, user)
                                "
                                small
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
