<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Arquivos
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert" v-if="successMsg.length">
                    <div class="flex">
                        <div>
                            <p class="text-sm">{{ successMsg }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <jet-secondary-button @click.native="openModal">
                        Adicionar Arquivo
                    </jet-secondary-button>
                    <br>
                    <div class="flex flex-col mt-5">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <input type="text" v-model="search" v-on:keyup="doTimerSearch" placeholder="Procurar...." class="form-input mt-1 mb-3 block w-full">
                                <div class="shadow overflow-hidden border-b border-gray-200">
                                    <vuetable ref="vuetable"
                                              :css="css"
                                              :api-url="'/files/json'"
                                              :append-params="appendParams"
                                              :fields="fields"
                                              data-path="data"
                                              pagination-path=""
                                              :per-page="perPage"
                                              noDataTemplate="Sem registros"
                                              @vuetable:pagination-data="onPaginationData"
                                    >
                                        <template slot="action-scope" slot-scope="props">
                                            <jet-secondary-button @click.native="() => loadData(props.rowData)">
                                                Editar
                                            </jet-secondary-button>
                                            <jet-danger-button @click.native="() => deleteFile(props.rowData.id)">
                                                Deletar
                                            </jet-danger-button>
                                        </template>
                                    </vuetable>
                                </div>
                                <div class="flex mt-2">
                                    <div class="flex-1 pt-2">
                                        Registros:
                                        <select v-model="perPage" v-on:change="perPageHandle">
                                            <option
                                                v-for="p in perPageOption"
                                                :value="p"
                                            >{{ p }}</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <vuetable-pagination ref="pagination"
                                                             :css="cssPagination"
                                                             @vuetable-pagination:change-page="onChangePage"
                                        ></vuetable-pagination>
                                    </div>
                                    <div class="flex-1 pt-2 text-right">
                                        <vuetable-pagination-info
                                            ref="paginationInfo"
                                            info-template="Mostrando: {from} - {to} de {total} registros"
                                            no-data-template="Sem informações"
                                        ></vuetable-pagination-info>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <dialog-modal :show="modal" maxWidth="6xl">
            <template #title>
                <h4 v-html="edit ? 'Editar Arquivo' : 'Novo Arquivo'"></h4>
            </template>
            <template #content>
                <div>
                    <div class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md" role="alert" v-if="errorMsg.length">
                        <div class="flex">
                            <div>
                                <p class="text-sm">{{ errorMsg }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div>
                            <label for="about" class="block text-sm font-medium text-gray-700">
                                Upload de Arquivo
                            </label>
                            <div class="mt-1">
                                <file-input
                                    @refresh-table="refreshTable"
                                    ref="uploadfile"
                                    :customers="$page.customers" :csrf="$page.csrf"></file-input>
                            </div>
                            <div v-if="$page.errors.name" class="text-red-500">{{ $page.errors.name[0] }}</div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <jet-secondary-button @click.native="closeModal">
                    Fechar
                </jet-secondary-button>
            </template>
        </dialog-modal>
    </app-layout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import moment from 'moment';
import DialogModal from "@/Jetstream/DialogModal";
import JetButton from "@/Jetstream/Button";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetInputError from "@/Jetstream/InputError";
import SwitchActive from "@/Components/SwitchActive";
import Vuetable from "vuetable-2";
import VuetablePagination from "vuetable-2/src/components/VuetablePagination";
import VuetablePaginationInfo from "vuetable-2/src/components/VuetablePaginationInfo";
import FileInput from "@/Components/FileInput";
import Swal from "sweetalert2";
import Vue from "vue";

export default {
    props: ['customers'],
    components: {
        AppLayout,
        Welcome,
        JetSecondaryButton,
        DialogModal,
        JetButton,
        JetDangerButton,
        JetActionMessage,
        JetInputError,
        SwitchActive,
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
        FileInput
    },
    data() {
        return {
            successMsg: '',
            errorMsg: '',
            errors: {},
            data: [],
            perPage: 10,
            search: '',
            idreg: 0,
            edit: false,
            modal: false,
            fields: [{
                name: 'name',
                title: 'Nome Arquivo',
                sortField: 'name'
            },
            {
                name: 'size',
                title: 'Tamanho',
                sortField: 'size',
                formatter: this.sizeFormatter,
            },
            {
                name: 'status',
                title: 'Status',
                sortField: 'status',
            },
            {
                name: 'updated_at',
                title: 'Atualizado em',
                formatter: this.formatDate,
                sortField: 'updated_at'
            },
            {
                name: 'action-scope',
                title: 'Ações'
            }],
            perPageOption: [10, 25, 50, 100, 200, 500],
            css: {
                tableWrapper: '',
                tableHeaderClass: 'bg-gray-600',
                tableBodyClass: 'bg-white divide-y divide-gray-200',
                tableClass: 'min-w-full divide-y divide-gray-200',
                loadingClass: 'loading',
                ascendingIcon: 'fa fa-chevron-up',
                descendingIcon: 'fa fa-chevron-down',
                ascendingClass: 'sorted-asc',
                descendingClass: 'sorted-desc',
                sortableIcon: 'fa fa-sort',
                detailRowClass: 'vuetable-detail-row',
                handleIcon: 'fa fa-bars text-secondary',
                renderIcon(classes, options) {
                    return `<i class="${classes.join(' ')}"></span>`
                }
            },
            cssPagination: {
                wrapperClass: '',
                activeClass: 'cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest mr-1 ml-1',
                disabledClass: 'disabled',
                pageClass: 'cursor-pointer inline-flex items-center px-4 py-2 bg-white border ml-1 mr-1 rounded-md text-xs font-semibold',
                linkClass: 'page-link',
                paginationClass: 'pagination',
                paginationInfoClass: 'float-left',
                dropdownClass: 'form-control',
                icons: {
                    first: 'cursor-pointer fa fa-angle-double-left',
                    prev: 'cursor-pointer fa fa-angle-left',
                    next: 'cursor-pointer fa fa-angle-right',
                    last: 'cursor-pointer fa fa-angle-double-right',
                }
            },
            appendParams: {},
        }
    },
    methods: {
        sizeFormatter (bytes) {
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            if (bytes == 0) return '0 Byte';
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },
        formatDate (value) {
            if (value === null) return '-';
            return moment(value).format('DD/MM/YYYY HH:ss');
        },
        deleteFile(id) {
            Swal.fire({
                title: 'Você tem certeza?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, deletar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/files/${id}`).then(response => {
                        const { data } = response;
                        if (data.success === true) {
                            Vue.$toast.success(data.message, {
                                duration: 3000,
                                position: 'top-right',
                                dismissible: true,
                            });
                            this.refreshTable();
                        }
                    })
                        .catch(error => {
                            Vue.$toast.error(error.message, {
                                duration: 3000,
                                position: 'top-right',
                                dismissible: true,
                            });
                        });
                }
            })
        },
        openModal() {
            this.modal = true;
        },
        closeModal() {
            this.modal = false;
        },
        refreshTable () {
            this.$nextTick(() => this.$refs.vuetable.refresh());
        },
        doFilter () {
            this.appendParams = {
                'search': this.search
            };
            this.$nextTick(() => this.$refs.vuetable.refresh());
        },
        doTimerSearch () {
            if (this.search.length) {
                clearTimeout(this.timer);
                this.timer = setTimeout(this.doFilter, 800);
            }
        },
        perPageHandle (value) {
            this.perPage = parseInt(value.target.value);
            this.$nextTick(() => this.$refs.vuetable.refresh());
        },
        onPaginationData(paginationData) {
            this.$refs.pagination.setPaginationData(paginationData)
            this.$refs.paginationInfo.setPaginationData(paginationData)
        },
        onChangePage(page) {
            this.$refs.vuetable.changePage(page);
        },
    }
}
</script>
