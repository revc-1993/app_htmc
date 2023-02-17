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
    commitments: Object,
});

// ---------------------------------------------------------
// PAGINACIÓN
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

// ---------------------------------------------------------
// VARIABLES
// ---------------------------------------------------------
let commitment;
const isModalActive = ref(false);
const operation = ref("");

// --------------------------------------------
// ABRIR MODAL: 1 Create, 2 Show, 3 Update, 4 Delete
// --------------------------------------------
const openModal = (action, commitments = "") => {
    // console.log(commitments);
    if (action !== "1") commitment = commitments;
    operation.value = action;
    isModalActive.value = true;
    // console.log(commitment);
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
</script>

<template>
    <!-- BOTON CREATE (OK) -->
    <!-- <BaseButtons type="justify-end">
        <BaseButton
            color="success"
            small
            :icon="mdiPenPlus"
            label="Crear compromiso"
            @click="openModal('1')"
        />
    </BaseButtons> -->

    <!-- <CardBoxModalCertification
        v-if="isModalActive"
        v-model="isModalActive"
        instance="certificación"
        :certification="certification"
        :departments="departments"
        :operation="operation"
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
                    <th class="text-center">N. Certificación</th>
                    <th class="text-center">Cod. Proceso</th>
                    <th class="text-center">Nombre de Proveedor</th>
                    <th class="text-center">Administrador de Contrato</th>
                    <th class="text-center">Monto a comprometer</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Usuario actual</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- <tr v-if="nItems == 0">
                    <td colspan="5">
                        <CardBoxComponentEmpty />
                    </td>
                </tr> -->
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
                    <td data-label="N. Certificación" class="text-center">
                        {{ commitment.certification.certification_number }}
                    </td>
                    <td data-label="Cod. Proceso" class="text-center">
                        {{ commitment.process_code }}
                    </td>
                    <td data-label="Proveedor" class="text-center">
                        {{ commitment.vendor_name }}
                    </td>
                    <td
                        data-label="Administrador de contrato"
                        class="text-center"
                    >
                        {{ commitment.contract_administrator }}
                    </td>
                    <td
                        data-label="Monto a comprometer"
                        class="text-center text-sm"
                    >
                        <strong>$ {{ commitment.amount_to_commit }}</strong>
                    </td>
                    <td data-label="Estado" class="py-3 px-6 text-center">
                        <SpanState :state="commitment.management_status" />
                    </td>
                    <td
                        data-label="Usuario"
                        class="text-center lg:w-1 whitespace-nowrap text-gray-500 dark:text-slate-400"
                    >
                        "-"
                        <strong> </strong>
                    </td>
                    <td
                        class="before:hidden lg:w-1 whitespace-nowrap text-center"
                    >
                        <BaseButtons
                            type="justify-start lg:justify-center whitespace-nowrap"
                            no-wrap
                        >
                            <BaseButton
                                color="info"
                                :icon="mdiEye"
                                small
                                @click="openModal('2', commitment)"
                            />
                            <BaseButton
                                color="warning"
                                :icon="mdiFindReplace"
                                small
                                @click="openModal('3', commitment)"
                            />
                            <BaseButton
                                color="danger"
                                :icon="mdiTrashCan"
                                small
                                @click="openModal('4', commitment)"
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
