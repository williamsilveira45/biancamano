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
                    <jet-secondary-button @click.native="">
                        Adicionar Cliente
                    </jet-secondary-button>
                    <br>
                    <table-vue
                        refTable="customerstable"
                        apiUrl="/customers/json"
                        :fields="fields"
                    >
                        <template v-slot:actions>
                            <jet-secondary-button @click.native="(row) => printRow(row)">
                                PRINT
                            </jet-secondary-button>
                        </template>
                    </table-vue>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import TableVue from "@/Components/TableVue";
import moment from 'moment';

export default {
    props: ['customers'],
    components: {
        AppLayout,
        Welcome,
        JetSecondaryButton,
        TableVue
    },
    data() {
        return {
            modal: false,
            form: this.$inertia.form({
                //
            }, {
                bag: 'deleteTeam'
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
            console.log(row);
        }
    }
}
</script>
