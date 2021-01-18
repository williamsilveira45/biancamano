<template>
<div>
    <vue-csv-import
        v-model="csv"
        :url="'/customers/'+$page.customer.id+'/arquivo'"
        :map-fields="['Conta']"
        :submit-btn-text="'Enviar'"
        :table-class="'vuetable w-8/12 border-b border-gray-100'"
        :headers="false"
        :file-mime-types="['text/csv']"
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
</div>
</template>

<script>
import Vue from "vue";
import {VueCsvToggleHeaders, VueCsvSubmit, VueCsvMap, VueCsvInput, VueCsvErrors, VueCsvImport} from 'vue-csv-import';
import JetButton from "@/Jetstream/Button";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";


export default {
    name: "PlanoDeContas",
    props: ['customer'],
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
    },
    data() {
        return {
            csv: '',
        };
    }
}
</script>

<style scoped>

</style>
