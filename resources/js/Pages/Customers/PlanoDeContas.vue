<template>
<div>
    <div>
        <jet-button @click.native="openModal">
            ADICIONAR ÚNICO
        </jet-button>
        <jet-secondary-button @click.native="toogleByFile" v-html="this.byfile ? 'VOLTAR À TABELA' : 'ADICIONAR POR ARQUIVO'" />
        <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert" v-if="successMsg.length">
            <div class="flex">
                <div>
                    <p class="text-sm">{{ successMsg }}</p>
                </div>
            </div>
        </div>
        <dialog-modal :show="modal">
            <template #title>
                <h4 v-html="edit ? 'Editar Conta' : 'Novo Conta'"></h4>
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
                                    Registro no Arquivo
                                </label>
                                <div class="mt-1">
                                    <input type="text" v-model="form.name" id="name" class="form-input block w-full" />
                                </div>
                                <div v-if="'name' in errors" class="text-red-500">{{ errors.name }}</div>
                            </div>
                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-700">
                                    Conta
                                </label>
                                <div class="mt-1">
                                    <div class="relative inline-block w-full text-gray-700">
                                        <select style="border-radius: 0.375rem" class="w-full h-10 pl-3 pr-6 text-base placeholder-gray-600 border appearance-none focus:shadow-outline" v-model="form.conta" placeholder="Conta">
                                            <option
                                                v-for="(p, pId) in plano_contas"
                                                :value="pId"
                                            >{{ p }}</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="'conta' in errors" class="text-red-500">{{ errors.conta }}</div>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
            <template #footer>
                <jet-secondary-button :class="{'float-left': true}" @click.native="closeModal">
                    Fechar
                </jet-secondary-button>
                <jet-button v-html="edit ? 'Editar' : 'Adicionar'"  @click.native="edit ? updateConta() : addConta()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" />
            </template>
        </dialog-modal>
        <div v-show="!byfile" class="mt-5">
            <div class="flex flex-col mt-5">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <input type="text" v-model="search" v-on:keyup="doTimerSearch" placeholder="Procurar...." class="form-input mt-1 mb-3 block w-full">
                        <div class="shadow overflow-hidden border-b border-gray-200">
                            <vuetable ref="vuetable"
                                      :css="css"
                                      :api-url="'/customers/'+this.customer.id+'/config/contas/jsoncontas'"
                                      :append-params="appendParams"
                                      :fields="fields"
                                      data-path="data"
                                      pagination-path=""
                                      :per-page="perPage"
                                      noDataTemplate="Sem registros"
                                      @vuetable:pagination-data="onPaginationData"
                            >
                                <template slot="active-scope" slot-scope="props">
                                    <switch-active :active="props.rowData.active"
                                                   :rowid="props.rowData.id"
                                                   :endp="`/customers/${props.rowData.customer_id}/config/contas/active`">
                                    </switch-active>
                                </template>
                                <template slot="action-scope" slot-scope="props">
                                    <jet-secondary-button @click.native="() => loadData(props.rowData)">
                                        Editar
                                    </jet-secondary-button>
                                    <jet-danger-button @click.native="() => deleteConta(props.rowData.id)">
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
        <div v-show="byfile" class="mt-5">
            <vue-csv-import
                v-model="csv"
                :url="'/customers/'+$page.customer.id+'/config/contas/readfile'"
                :map-fields="['Conta']"
                :submit-btn-text="'Enviar'"
                :table-class="'vuetable w-8/12 border-b border-gray-100'"
                :headers="true"
                :file-mime-types="['text/csv']"
                :callback="(data) => read(data, 'callback')"
                :can-ignore="true"
            >
                <template slot="error">
                    <div style="margin-top: 1em;margin-bottom: 1em;color:#F00;">
                        Tipo de arquivo errado.
                    </div>
                </template>

                <template slot="thead">
                    <tr>
                        <th class="border-b border-gray-100 p-5">Campos</th>
                        <th class="border-b border-gray-100 p-5">Colunas do Arquivo</th>
                    </tr>
                </template>

                <template slot="next" slot-scope="{load}">
                    <div style="margin-top: 1em;margin-bottom: 1em;">
                        <jet-secondary-button @click.native="load">
                            Carregar
                        </jet-secondary-button>
                    </div>
                </template>

                <template slot="submit" slot-scope="{submit}">
                    <div style="margin-top: 1em;margin-bottom: 1em;">
                        <jet-button @click.native="submit">
                            Enviar
                        </jet-button>
                    </div>
                </template>
            </vue-csv-import>

            <div class="mt-4 border-top-1" v-if="contas.length">
                <table class="vuetable w-8/12 border-b border-gray-100">
                    <thead>
                    <tr>
                        <th class="border-b border-gray-100 p-5">Arquivo CSV</th>
                        <th class="border-b border-gray-100 p-5">Conta do Sistema</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(conta, index) in contas" :key="index">
                        <td>{{conta}}</td>
                        <td>
                            <select style="border: 1px solid #CCC;" class="p-1" v-model="conta_sistema[index]">
                                <option
                                    v-for="(p, pId) in plano_contas"
                                    :value="pId"
                                >{{ p }}</option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <jet-button @click.native="sendToDB">
                    Associar Planos de Conta
                </jet-button>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import Vue from "vue";
import {VueCsvToggleHeaders, VueCsvSubmit, VueCsvMap, VueCsvInput, VueCsvErrors, VueCsvImport} from 'vue-csv-import';
import JetButton from "@/Jetstream/Button";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import Swal from 'sweetalert2'
import moment from "moment";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetInputError from "@/Jetstream/InputError";
import SwitchActive from "@/Components/SwitchActive";
import Vuetable from "vuetable-2";
import VuetablePagination from "vuetable-2/src/components/VuetablePagination";
import VuetablePaginationInfo from "vuetable-2/src/components/VuetablePaginationInfo";
import DialogModal from "@/Jetstream/DialogModal";


export default {
    name: "PlanoDeContas",
    props: ['customer', 'plano_contas'],
    components: {
        Vue,
        VueCsvToggleHeaders,
        VueCsvSubmit,
        VueCsvMap,
        VueCsvInput,
        VueCsvErrors,
        VueCsvImport,
        JetButton,
        JetDangerButton,
        JetSecondaryButton,
        Swal,
        JetActionMessage,
        JetInputError,
        SwitchActive,
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
        DialogModal,
    },
    data() {
        return {
            csv: '',
            byfile: false,
            contas: [],
            conta_sistema: [],
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
                name: this.name,
                conta: this.conta,
            }, {
                bag: 'addConta'
            }),
            fields: [{
                    name: 'nome_csv',
                    title: 'Coluna do Arquivo',
                    sortField: 'nome_csv'
                },
                {
                    name: 'nome_conta',
                    title: 'Conta',
                    sortField: 'nome_conta'
                },
                {
                    name: 'updated_at',
                    title: 'Atualizado em',
                    formatter: this.formatDate,
                    sortField: 'updated_at'
                },
                {
                    name: 'active-scope',
                    title: 'Ativado'
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
        };
    },
    methods: {
        toogleByFile() {
            this.byfile = !this.byfile;
        },
        read(data, type) {
            if (type === 'callback') {
                this.contas = data.data[0];
            }
        },
        sendToDB() {
            let url = `/customers/${this.customer.id}/config/contas/regcontas`;
            axios.post(url, {
                contas: this.contas,
                contas_sistema: this.conta_sistema,
            })
            .then(response => {
                if (response.data.success===true) {
                    Swal.fire("Sucesso!", response.data.message, "success");
                    this.refreshTable();
                    this.toogleByFile();

                } else {
                    Swal.fire("Erro", response.data.message, "error");
                }
            })
            .catch(error => {
                Vue.$toast.error(error.message, {
                    duration: 3000,
                    position: 'top-right',
                    dismissible: true,
                });
            });
        },
        redirectPage(customerId) {
            window.location.href="/customers/"+customerId+"/config";
        },
        formatDate (value) {
            if (value === null) return '-';
            return moment(value).format('DD/MM/YYYY HH:ss');
        },
        addConta() {
            this.resetErrors();
            axios.post(`/customers/${this.customer.id}/config/contas/store`, this.form).then((response) => {
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
        updateConta() {
            this.resetErrors();
            axios.put(`/customers/${this.customer.id}/config/contas/${this.idreg}`, this.form).then( (response) => {
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
            this.form.name = '';
            this.form.conta = '';
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
        deleteConta(id) {
            Swal.fire({
                title: 'Você tem certeza?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, deletar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/customers/${this.customer.id}/config/contas/${id}`).then(response => {
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
            this.form.name = data.nome_csv;
            this.form.conta = data.conta_id;
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
    },
}
</script>

<style scoped>

</style>
