<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Relatório Vencimento
            </h2>
        </template>

        <div class="py-5">
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8" style="height: 900px">
                    <loading :active.sync="isLoading"
                             :can-cancel="false"
                             :is-full-page="false"></loading>
                    <Pivot
                        ref="pivot"
                        toolbar
                        :height="'800px'"
                        :report="{
                            localization:  '/assets/pt-br.json',
                            'dataSource': {
                                'type': 'json',
                                'filename': '/reports/json/28/vencimento',
                                'useStreamLoader': true,
                            },
                            'slice': {
                                'rows': [
                                    {
                                        'uniqueName': 'conta_sistema'
                                    }
                                ],
                                'columns': [
                                    {
                                        'uniqueName': 'data_vencimento_original.Year',
                                        'caption': 'Data Venc. Ano'
                                    },
                                    {
                                        'uniqueName': 'data_vencimento_original.Month',
                                        'caption': 'Data Venc. Mês'
                                    },
                                    {
                                        'uniqueName': '[Measures]'
                                    }
                                ],
                                'measures': [
                                    {
                                        'uniqueName': 'valor',
                                        'aggregation': 'sum',
                                        'format': 'currency'
                                    }
                                ],
                                'sorting': {
                                    'column': {
                                        'type': 'asc',
                                        'tuple': [
                                            'data_vencimento_original.year.[2016]'
                                        ],
                                        'measure': {
                                            'uniqueName': 'valor',
                                            'aggregation': 'sum'
                                        }
                                    }
                                }
                            },
                            'formats': [
                                {
                                    'name': 'currency',
                                    'thousandsSeparator': '.',
                                    'decimalSeparator': ',',
                                    'decimalPlaces': 2,
                                    'currencySymbol': 'R$ '
                                }
                            ],
                        }"
                    >
                    </Pivot>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout'
import moment from 'moment';
import Swal from "sweetalert2";
import Vue from "vue";
import Loading from 'vue-loading-overlay';
import {Pivot} from "vue-flexmonster";
import 'flexmonster/flexmonster.css';
import 'vue-loading-overlay/dist/vue-loading.css';

export default {
    name: 'ReportsVencimento',
    components: {
        AppLayout,
        Pivot,
        Loading,
    },
    data() {
        return {
            isLoading: false,
            dados: null,
        }
    },
    methods: {
        onReady: function () {
            //Connect Flexmonster to the data
            axios.get('/reports/json/28/vencimento')
                .then((response) => {
                    this.dados = response.data;
                    // this.$refs.pivot.flexmonster.updateData({ data: response.data });
                    this.isLoading = false;
                })
                .catch((error) => {
                    Swal.fire("Erro", error, "error");
                    this.isLoading = false;
                });
        },
    }
}
</script>
