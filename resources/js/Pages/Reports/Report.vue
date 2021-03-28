<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ customer.name }} - Relatório Vencimento
            </h2>
        </template>

        <div class="py-5">
            <loading :active.sync="isLoading"
                     :can-cancel="false"
                     :is-full-page="false"></loading>
            <div class="w-full mx-auto sm:px-6" style="z-index: 1000">
                <dialog-modal :show="modal" @close="closeModal" maxWidth="6xl">
                    <template #title>
                        <h4>Relatório Detalhado</h4>
                    </template>
                    <template #content>
                        <Pivot
                            ref="pivotModal"
                            toolbar
                            :height="'800px'"
                            :report="{
                                localization:  '/assets/pt-br.json',
                            }"
                        >
                        </Pivot>
                    </template>
                    <template #footer>
                        <jet-secondary-button @click.native="closeModal">
                            Fechar
                        </jet-secondary-button>
                    </template>
                </dialog-modal>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8" style="height: 900px;z-index: 0">
                    <Pivot
                        ref="pivot"
                        toolbar
                        v-bind:customizeContextMenu="rightClickMenu"
                        :height="'800px'"
                        :report="{
                            localization:  '/assets/pt-br.json',
                            'dataSource': {
                                'type': 'json',
                                'filename': `/reports/${customer.id}/${tipo}/json`,
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
import DialogModal from "@/Jetstream/DialogModal";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";

export default {
    name: 'ReportsVencimento',
    props: ['customer', 'tipo'],
    components: {
        AppLayout,
        Pivot,
        Loading,
        DialogModal,
        JetSecondaryButton,
    },
    data() {
        return {
            isLoading: false,
            dados: null,
            modal: false,
        }
    },
    methods: {
        rightClickMenu (items, data, viewType) {
            if (data.type == "value")
            {
                items.push({
                    label: "Visualizar Além dos Detalhes",
                    handler: () => this.openDetails(data)
                });
            }
            return items;
        },
        openDetails(data) {
            // console.log(data, this.$refs.pivotModal.flexmonster.getReportFilters());
            // return;
            this.openModal();
        },
        openModal() {
            this.modal = true;
            let flex = this.$refs.pivotModal.flexmonster;
            flex.clear();
            let backFlex = this.$refs.pivot.flexmonster;
            let detailData = backFlex.getReport();

            detailData.dataSource = {
                filename: `/reports/${this.customer.id}/${this.tipo}/json`,
                type: "json",
                useStreamLoader: true,
            };

            flex.setReport(detailData);
            flex.refresh();
        },
        closeModal() {
            this.modal = false;

        },
        onReady: function () {
            //Connect Flexmonster to the data
            axios.get(`/reports/${this.customer.id}/${this.relatorio}/${this.tipo}/json`)
                .then((response) => {
                    this.dados = response.data;
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
