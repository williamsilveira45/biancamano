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
        }
    }
}
</script>
