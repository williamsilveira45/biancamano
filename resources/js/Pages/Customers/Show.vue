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
                            <jet-secondary-button @click.native="() => printRow(row)">
                                Print
                            </jet-secondary-button>
                        </template>
                    </table-vue>
                </div>
            </div>
        </div>
        <dialog-modal :show="modal">
            <template #title>
                <h4>Novo Cliente</h4>
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
<!--                                <jet-input-error :message="form.error('name')" class="mt-2 text-purple-500" />-->
                            </div>
                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-700">
                                    CNPJ
                                </label>
                                <div class="mt-1">
                                    <input type="text" v-mask="'##.###.###/####-##'" v-model="form.cnpj" id="cnpj" class="form-input block w-full" />
                                </div>
                                <div v-if="$page.errors.cnpj" class="text-red-500">{{ $page.errors.cnpj[0] }}</div>
<!--                                <jet-input-error :message="form.error('cnpj')" class="mt-2 text-purple-500" />-->
                            </div>
                        </div>
                    </div>
                </form>
            </template>
            <template #footer>
                <jet-secondary-button :class="{'float-left': true}" @click.native="closeModal">
                    Fechar
                </jet-secondary-button>
                <jet-button  @click.native="createCustomer" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Adicionar
                </jet-button>
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
import JetInputError from "@/Jetstream/InputError";

export default {
    props: ['customers'],
    components: {
        AppLayout,
        Welcome,
        JetSecondaryButton,
        TableVue,
        DialogModal,
        JetButton,
        JetActionMessage,
        JetInputError,
    },
    data() {
        return {
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
                title: 'Ativado'
            }],
        }
    },
    methods: {
        formatDate (value) {
            if (value === null) return '-';
            return moment(value).format('DD/MM/YYYY HH:ss');
        },
        printRow (row) {
            // this.$refs.customerstable.refreshTable();
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
        reset() {
            this.form.name = ''
            this.form.phone = ''
        },
        openModal() {
            this.modal = true;
        },
        closeModal() {
            this.modal = false;
        }
    }
}
</script>
