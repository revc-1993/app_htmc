<script setup>
import { computed, ref } from "vue";
import { mdiEye, mdiPenPlus, mdiTrashCan, mdiFindReplace } from "@mdi/js";
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue";
import TableCheckboxCell from "@/components/TableCheckboxCell.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import BaseButton from "@/components/BaseButton.vue";
import SpanState from "@/components/SpanState.vue";
import CardBoxModalCertification from "@/components/certifications/CardBoxModalCertification.vue";
import CardBox from "@/components/CardBox.vue";

const props = defineProps({
    checkable: Boolean,
    certifications: Object,
    departments: Object,
    process_types: Object,
    expense_types: Object,
    budget_lines: Object,
    users: Object,
    record_statuses: Object,
});

// ---------------------------------------------------------
// PAGINACIÓN
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

// ---------------------------------------------------------
// VARIABLES
// ---------------------------------------------------------
const certification = ref({});
const isModalActive = ref(false);
const operation = ref("");

// --------------------------------------------
// ABRIR MODAL: 1 Create, 2 Show, 3 Update, 4 Delete
// --------------------------------------------
const openModal = (action, certifications = "") => {
    // console.log(certifications);
    if (action !== "1") certification.value = certifications;
    operation.value = action;
    isModalActive.value = true;
    // console.log(certification);
    // console.log(operation.value);
};

// --------------------------------------------
// CERRAR MODAL: CONFIRMAR O CANCELAR
// --------------------------------------------
const closeModal = (isconfirm) => {
    if (isconfirm === "confirm") alert(operation.value);
    isModalActive.value = false;
    operation.value = "";
};

// --------------------------------------------
// ESTADO DE CERTIFICACION
// --------------------------------------------
const current_managements = {
    1: "1. Secretaría CGF",
    2: "2. Secretaría JAPC",
    3: "3. Analista de Certificación",
    4: "4. Analista de Tesorería",
};
</script>

<template>
    <!-- BOTON CREATE (OK) -->
    <BaseButtons
        type="justify-end"
        v-if="$page.props.user.permissions.includes('create_certification')"
    >
        <BaseButton
            color="success"
            small
            :icon="mdiPenPlus"
            label="Crear certificación"
            @click="openModal('1')"
        />
    </BaseButtons>

    <CardBoxModalCertification
        v-if="isModalActive"
        v-model="isModalActive"
        instance="certificación"
        :certification="certification"
        :departments="departments"
        :process_types="process_types"
        :expense_types="expense_types"
        :budget_lines="budget_lines"
        :users="users"
        :record_statuses="record_statuses"
        :operation="operation"
        @confirm="closeModal"
    />

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
                    <th class="text-center">Objeto de contrato</th>
                    <th class="text-center">Area requirente</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Monto certificado</th>
                    <th class="text-center">Gestión actual</th>
                    <th class="text-center">Usuario asignado</th>
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
                        v-if="checkable"
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
                        <SpanState
                            v-if="certification.record_status"
                            :state="certification.record_status"
                        />
                        <div v-else>-</div>
                    </td>
                    <td data-label="Monto certificado" class="text-center">
                        <strong v-if="certification.certified_amount"
                            >$ {{ certification.certified_amount }}</strong
                        >
                        <div v-else>-</div>
                    </td>
                    <td
                        data-label="Gestión actual"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong>
                            {{
                                current_managements[
                                    certification.current_management
                                ]
                            }}
                        </strong>
                    </td>
                    <td
                        data-label="Usuario asignado"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        <strong v-if="certification.user">
                            @{{ certification.user.username }}
                        </strong>
                        <div v-else>-</div>
                    </td>
                    <td
                        class="before:hidden lg:w-1 whitespace-nowrap text-center"
                    >
                        <BaseButtons
                            type="justify-start lg:justify-center whitespace-nowrap"
                            no-wrap
                        >
                            <BaseButton
                                v-if="
                                    $page.props.user.permissions.includes(
                                        'show_certification'
                                    )
                                "
                                color="info"
                                :icon="mdiEye"
                                small
                                @click="openModal('2', certification)"
                            />
                            <BaseButton
                                v-if="
                                    $page.props.user.permissions.includes(
                                        'update_certification'
                                    )
                                "
                                color="warning"
                                :icon="mdiFindReplace"
                                small
                                @click="openModal('3', certification)"
                            />
                            <BaseButton
                                v-if="
                                    $page.props.user.permissions.includes(
                                        'delete_certification'
                                    )
                                "
                                color="danger"
                                :icon="mdiTrashCan"
                                small
                                @click="openModal('4', certification)"
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
                    >Página {{ currentPageHuman > 0 ? currentPage + 1 : 0 }} de
                    {{ numPages }}</small
                >
            </BaseLevel>
        </div>
    </CardBox>
</template>
