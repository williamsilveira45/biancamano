<template>
<div>
    <vue-csv-import
        v-model="csv"
        :url="'/customers/'+$page.customer.id+'/config/readfile'"
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
</template>

<script>
import Vue from "vue";
import {VueCsvToggleHeaders, VueCsvSubmit, VueCsvMap, VueCsvInput, VueCsvErrors, VueCsvImport} from 'vue-csv-import';
import JetButton from "@/Jetstream/Button";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import Swal from 'sweetalert2'


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
    },
    data() {
        return {
            csv: '',
            contas: [],
            conta_sistema: [],
        };
    },
    methods: {
        read(data, type) {
            if (type === 'callback') {
                this.contas = data.data[0];
            }
        },
        sendToDB() {
            let url = `/customers/${this.customer.id}/config/regcontas`;
            axios.post(url, {
                contas: this.contas,
                contas_sistema: this.conta_sistema,
            })
            .then(response => {
                if (response.data.success===true) {
                    Swal.fire("Sucesso!", response.response.message, "success");
                } else {
                    Swal.fire("Erro", response.response.message, "error");
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
    }
}
</script>

<style scoped>

</style>
