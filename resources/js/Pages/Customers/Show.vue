<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Clientes
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert" v-if="$page.flash.message">
                    <div class="flex">
                        <div>
                            <p class="text-sm">{{ $page.flash.message }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <jet-secondary-button @click.native="openModal">
                        Adicionar Cliente
                    </jet-secondary-button>
                    <br>
                    <table-vue
                        ref="customerstable"
                        apiUrl="/customers/json"
                        :fields="fields"
                    >
                        <template slot="actions" slot-scope="{ row }">
                            <jet-secondary-button @click.native="() => loadData(row)">
                                Editar
                            </jet-secondary-button>
                            <jet-danger-button @click.native="() => deleteCustomer(row.id)">
                                Deletar
                            </jet-danger-button>
                        </template>
                    </table-vue>
                </div>
            </div>
        </div>
        <dialog-modal :show="modal">
            <template #title>
                <h4 v-html="edit ? 'Editar Cliente' : 'Novo Cliente'"></h4>
            </template>
            <template #content>
                <form action="#" method="POST">
                    <div>
                        <div class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md" role="alert" v-if="$page.flash.errorMessage">
                            <div class="flex">
                                <div>
                                    <p class="text-sm">{{ $page.flash.errorMessage }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-700">
                                    Nome do Cliente
                                </label>
                                <div class="mt-1">
                                    <input type="text" v-model="form.name" id="name" class="form-input block w-full" />
                                </div>
                                <div v-if="$page.errors.name" class="text-red-500">{{ $page.errors.name[0] }}</div>
                            </div>
                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-700">
                                    CNPJ
                                </label>
                                <div class="mt-1">
                                    <input type="text" v-mask="'##.###.###/####-##'" v-model="form.cnpj" id="cnpj" class="form-input block w-full" />
                                </div>
                                <div v-if="$page.errors.cnpj" class="text-red-500">{{ $page.errors.cnpj[0] }}</div>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
            <template #footer>
                <jet-secondary-button :class="{'float-left': true}" @click.native="closeModal">
                    Fechar
                </jet-secondary-button>
                <jet-button v-html="edit ? 'Editar' : 'Adicionar'"  @click.native="edit ? updateCustomer() : createCustomer()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" />
            </template>
        </dialog-modal>
    </app-layout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import TableVue from "@/Components/TableVue";
import moment from 'moment';
import DialogModal from "@/Jetstream/DialogModal";
import JetButton from "@/Jetstream/Button";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetInputError from "@/Jetstream/InputError";
import Swal from 'sweetalert2'
import SwitchActive from "@/Components/SwitchActive";

export default {
    props: ['customers'],
    components: {
        AppLayout,
        Welcome,
        JetSecondaryButton,
        TableVue,
        DialogModal,
        JetButton,
        JetDangerButton,
        JetActionMessage,
        JetInputError,
        SwitchActive,
    },
    data() {
        return {
            idreg: 0,
            edit: false,
            modal: false,
            form: this.$inertia.form({
                name: this.name,
                cnpj: this.cnpj,
            }, {
                bag: 'createCustomer'
            }),
            fields: [{
                name: 'name',
                title: 'Nome',
                sortField: 'name'
            },
            {
                name: 'cnpj',
                title: 'CNPJ',
                sortField: 'cnpj'
            },
            {
                name: 'created_at',
                title: 'Criado em',
                formatter: this.formatDate,
                sortField: 'created_at'
            },
            {
                name: 'updated_at',
                title: 'Atualizado em',
                formatter: this.formatDate,
                sortField: 'updated_at'
            },
            {
                name: 'actions',
                title: 'Ações'
            }],
        }
    },
    methods: {
        formatDate (value) {
            if (value === null) return '-';
            return moment(value).format('DD/MM/YYYY HH:ss');
        },
        createCustomer() {
            this.$inertia.post(route('customers.store'), this.form).then( () => {
                if (Object.keys(this.$page.errors).length === 0 && this.$page.flash.errorMessage === null) {
                    this.reset();
                    this.closeModal();
                    this.$refs.customerstable.refreshTable();
                }
            })
        },
        updateCustomer() {
            this.$inertia.put('/customers/' + this.idreg, this.form).then( () => {
                if (Object.keys(this.$page.errors).length === 0 && this.$page.flash.errorMessage === null) {
                    this.reset();
                    this.closeModal();
                    this.$refs.customerstable.refreshTable();
                }
            })
        },
        reset() {
            this.form.name = '';
            this.form.cnpj = '';
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
                    this.$inertia.delete('/customers/' + id);
                    Swal.fire(
                        'Deletado!',
                        'Registro deletado com sucesso.',
                        'success'
                    )
                    this.$refs.customerstable.refreshTable();
                }
            })
        },
        loadData(data) {
            console.log(data);
            this.idreg = data.id;
            this.edit = true;
            this.form.name = data.name;
            this.form.cnpj = data.cnpj;
            this.openModal();
        }
    }
}
</script>
