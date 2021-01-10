<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Plano de Contas
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
                        Adicionar Conta
                    </jet-secondary-button>
                    <br>
                    <div class="flex flex-col mt-5">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <input type="text" v-model="search" v-on:keyup="doTimerSearch" placeholder="Procurar...." class="form-input mt-1 mb-3 block w-full">
                                <div class="shadow overflow-hidden border-b border-gray-200">
                                    <vuetable ref="vuetable"
                                              :css="css"
                                              :api-url="'/sistema/pconta/json'"
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
                                            <jet-danger-button @click.native="() => deleteCustomer(props.rowData.id)">
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
        <dialog-modal :show="modal">
            <template #title>
                <h4 v-html="edit ? 'Editar Conta' : 'Nova Conta'"></h4>
            </template>
            <template #content>
                <form action="#" method="POST">
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
                                    Nome da Conta
                                </label>
                                <div class="mt-1">
                                    <input type="text" v-model="form.nome_conta" id="nome_conta" class="form-input block w-full" />
                                </div>
                                <div v-if="'nome_conta' in errors" class="text-red-500">{{ errors.nome_conta }}</div>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
            <template #footer>
                <jet-secondary-button :class="{'float-left': true}" @click.native="closeModal">
                    Fechar
                </jet-secondary-button>
                <jet-button v-html="edit ? 'Editar' : 'Adicionar'"  @click.native="edit ? update() : create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" />
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
import Swal from 'sweetalert2'
import SwitchActive from "@/Components/SwitchActive";
import Vuetable from "vuetable-2";
import VuetablePagination from "vuetable-2/src/components/VuetablePagination";
import VuetablePaginationInfo from "vuetable-2/src/components/VuetablePaginationInfo";
import Vue from "vue";

export default {
    props: ['planocontas'],
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
            form: this.$inertia.form({
                nome_conta: this.nome_conta,
            }, {
                bag: 'create'
            }),
            fields: [{
                name: 'nome_conta',
                title: 'Nome Conta',
                sortField: 'nome_conta'
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
        formatDate (value) {
            if (value === null) return '-';
            return moment(value).format('DD/MM/YYYY HH:ss');
        },
        create() {
            this.resetErrors();
            axios.post("/sistema/pconta/store", this.form).then( (response) => {
                const { data } = response;

                if (data.success) {
                    this.reset();
                    this.closeModal();
                    this.refreshTable();
                    this.successMsg = data.message;
                    return;
                }

                if (!data.success && data.type === 'inputError') {
                    this.errors = data.errors;
                }

                this.errorMsg = data.message;
            })
        },
        update() {
            this.resetErrors();
            axios.put("/sistema/pconta/" + this.idreg, this.form).then( (response) => {
                const { data } = response;

                if (data.success) {
                    this.reset();
                    this.closeModal();
                    this.refreshTable();
                    this.successMsg = data.message;
                    return;
                }

                if (!data.success && data.type === 'inputError') {
                    this.errors = data.errors;
                }

                this.errorMsg = data.message;
            })
        },
        reset() {
            this.form.nome_conta = '';
            this.resetErrors();
        },
        resetErrors() {
            this.successMsg = '';
            this.errorMsg = '';
            this.errors = {};
        },
        openModal() {
            this.modal = true;
        },
        closeModal(reset) {
            this.modal = false;
            if (reset) {
                this.edit = false;
                this.reset();
            }
        },
        deleteCustomer(id) {
            Swal.fire({
                title: 'Você tem certeza?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, deletar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/sistema/pconta/' + id).then(response => {
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
        loadData(data) {
            this.idreg = data.id;
            this.edit = true;
            this.form.nome_conta = data.nome_conta;
            this.openModal();
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
