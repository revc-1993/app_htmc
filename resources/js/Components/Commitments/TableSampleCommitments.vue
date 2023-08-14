<script setup>
import { computed, ref } from "vue";
import {
    mdiEye,
    mdiPenPlus,
    mdiTrashCan,
    mdiUpdate,
    mdiFileDelimited,
    mdiFileExcel,
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
    return usePage().props.user.permissions.includes(permission);
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
                :icon="mdiFileExcel"
                :tooltip="'Exportar a Excel'"
            />
            <ExportButton
                :data="commitments"
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
            <!-- BUTTON CON CRUD MODAL -->
            <!-- <BaseButton
                :color="elementProps.create.color"
                :icon="elementProps.create.icon"
                :label="elementProps.create.label"
                :tooltip="elementProps.create.tooltip"
                @click="openModal(elementProps.create.tag)"
                small
                v-if="
                    $page.props.user.permissions.includes(
                        'create_certification'
                    )
                "
            /> -->
            <!-- BUTTON HACIA OTRA PAGINA -->
            <BaseButton
                :color="elementProps.create.color"
                :icon="elementProps.create.icon"
                :label="elementProps.create.label"
                :tooltip="elementProps.create.tooltip"
                route-name="commitments.create"
                small
                v-show="hasPermissions('create_commitment')"
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
                    <th class="text-center">N.</th>
                    <th class="text-center">N. Certif.</th>
                    <th class="text-center">Administrador de contrato</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">N. Compromiso</th>
                    <th class="text-center">Monto comprometido</th>
                    <th class="text-center">Saldo</th>
                    <th class="text-center">Gestión actual</th>
                    <th class="text-center">Asignado</th>
                    <th class="text-center">Tiempo</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="commitment in itemsPaginated"
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
                        <template v-if="commitment.certification">
                            {{ commitment.certification.certification_number }}
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td
                        data-label="Administrador de contrato"
                        class="text-center"
                    >
                        {{ commitment.contract_administrator }}
                    </td>
                    <td data-label="Estado" class="py-3 px-6 text-center">
                        <template v-if="commitment.record_status">
                            <SpanState :state="commitment.record_status" />
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td data-label="Nro. Comp." class="text-center">
                        <template v-if="commitment.commitment_cur">
                            <strong>
                                {{ commitment.commitment_cur }}
                            </strong>
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td data-label="Monto" class="text-center">
                        <template v-if="commitment.commitment_amount">
                            <strong>
                                {{
                                    formattedNumber(
                                        commitment.commitment_amount
                                    )
                                }}</strong
                            >
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td data-label="Saldo" class="text-center">
                        <template v-if="commitment.balance">
                            <strong>
                                {{
                                    formattedNumber(commitment.balance)
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
                            {{ roles[commitment.current_management - 1].id }}.
                            {{
                                roles[commitment.current_management - 1]
                                    .nickname
                            }}
                        </strong>
                    </td>
                    <td
                        data-label="Asignado a"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <template v-if="commitment.user">
                            <strong> @{{ commitment.user.username }} </strong>
                        </template>
                        <template v-else>-</template>
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
                                v-show="hasPermissions('show_commitment')"
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
                                v-show="hasPermissions('update_commitment')"
                                route-name="commitments.edit"
                                :id="commitment.id"
                            />
                            <BaseButton
                                :color="elementProps.delete.color"
                                :icon="elementProps.delete.icon"
                                :tooltip="elementProps.delete.tooltip"
                                small
                                v-show="hasPermissions('delete_commitment')"
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
