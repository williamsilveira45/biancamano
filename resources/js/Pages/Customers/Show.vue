<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Clientes
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <jet-secondary-button @click.native="modal=true">
                        Adicionar Cliente
                    </jet-secondary-button>
                    <br>
                    <table-vue
                        refTable="customerstable"
                        apiUrl="/customers/json"
                        :fields="fields"
                    >
                        <template v-slot:actions>
                            <div slot="active-scope" slot-scope="props">
                                <p>Testing</p>
                            </div>
                        </template>
<!--                            <template slot="action-scope" slot-scope="props">-->
<!--                                <div class="btn-group" role="group" aria-label="Basic example">-->
<!--                                    <a :href="'/admin/users/'+props.rowData.id+'/edit'" type="button" class="btn btn-sm btn-warning">Editar</a>-->
<!--        &lt;!&ndash;                                <delete-reg route="/admin/users/destroy"&ndash;&gt;-->
<!--        &lt;!&ndash;                                            :rowid="props.rowData.id"&ndash;&gt;-->
<!--        &lt;!&ndash;                                            @callback="refreshTable"&ndash;&gt;-->
<!--        &lt;!&ndash;                                ></delete-reg>&ndash;&gt;-->
<!--                                </div>-->
<!--                            </template>-->
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
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-700">
                                    Nome do Cliente
                                </label>
                                <div class="mt-1">
                                    <input type="text" v-model="form.name" id="name" class="form-input block w-full" />
                                </div>
                                <jet-input-error :message="form.error('name')" class="mt-2" />
                            </div>
                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-700">
                                    CNPJ
                                </label>
                                <div class="mt-1">
                                    <input type="text" v-mask="'### ### ###'" v-model="form.cnpj" id="cnpj" class="form-input block w-full" />
                                </div>
                                <jet-input-error :message="form.error('cnpj')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </form>
            </template>
            <template #footer>
                <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                    Saved.
                </jet-action-message>
                <jet-secondary-button :class="{'float-left': true}" @click.native="modal = false">
                    Fechar
                </jet-secondary-button>
                <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
    components: {
        AppLayout,
        Welcome,
        JetSecondaryButton,
        TableVue,
        DialogModal,
        JetButton,
        JetActionMessage,
        JetInputError,
        VueMaskDirective,
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
        createCustomer() {

        }
    }
}
</script>
