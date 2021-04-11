<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ customer.name }} - Relatório <span>{{relatorio}} {{tipo}} | {{year}}</span>
            </h2>
        </template>

        <div class="py-5">
            <loading :active.sync="isLoading"
                     :can-cancel="false"
                     :is-full-page="false"></loading>
            <div class="w-full mx-auto sm:px-6" style="z-index: 1000">
                <dialog-modal :show="yearModal" @close="closeModalYear" max-width="sm">
                    <template #title>
                        <h4>Selecionar ano</h4>
                    </template>
                    <template #content>
                        <div class="relative inline-block w-full text-gray-700">
                            <select style="border-radius: 0.375rem" class="w-full h-10 pl-3 pr-6 text-base placeholder-gray-600 border appearance-none focus:shadow-outline" v-model="year" placeholder="Ano">
                                <option
                                    v-for="y in years"
                                    :value="y"
                                >{{ y }}</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                            </div>
                        </div>
                    </template>
                    <template #footer>
                        <jet-secondary-button @click.native="closeModalYear">
                            Fechar
                        </jet-secondary-button>
                        <jet-button @click.native="confirmYearChange()">
                            Confirmar
                        </jet-button>
                    </template>
                </dialog-modal>
                <dialog-modal :show="modal" @close="closeModal" maxWidth="6xl">
                    <template #title>
                        <h4>Relatório Detalhado</h4>
                    </template>
                    <template #content>
                        <Pivot
                            ref="pivotModal"
                            toolbar
                            :height="'800px'"
                            :license-key="license"
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
                        v-bind:beforetoolbarcreated="customizeToolbar"
                        :height="'800px'"
                        :license-key="license"
                        :report="{
                            localization:  '/assets/pt-br.json',
                            'dataSource': {
                                'type': 'json',
                                'filename': `/reports/${customer.id}/${relatorio}/${tipo}/json/${year}`,
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
import JetButton from '@/Jetstream/Button'

export default {
    name: 'ReportsVencimento',
    props: ['customer', 'tipo', 'relatorio', 'license', 'years'],
    components: {
        AppLayout,
        Pivot,
        Loading,
        DialogModal,
        JetSecondaryButton,
        JetButton
    },
    data() {
        return {
            isLoading: false,
            dados: null,
            modal: false,
            yearModal: false,
            year: null,
        }
    },
    beforeMount() {
        this.year = this.years[0];
    },
    methods: {
        customizeToolbar (toolbar) {
            const obj = this;
            // get all tabs
            var tabs = toolbar.getTabs();
            toolbar.getTabs = function ()
            {
                // add new tab
                tabs[tabs.length + 1] =
                    {
                        id: "fm-tab-filtro",
                        title: "Filtro",
                        handler: obj.openModalYear,
                        icon: this.icons.fields
                    };
                return tabs;
            }
        },
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
                filename: `/reports/${this.customer.id}/${this.relatorio}/${this.tipo}/json`,
                type: "json",
                useStreamLoader: true,
            };

            flex.setReport(detailData);
            flex.refresh();
        },
        closeModal() {
            this.modal = false;
        },
        confirmYearChange () {
            let flex = this.$refs.pivot.flexmonster;
            let detailData = flex.getReport();

            detailData.dataSource = {
                filename: `/reports/${this.customer.id}/${this.relatorio}/${this.tipo}/json/${this.year}`,
                type: "json",
                useStreamLoader: true,
            };

            flex.setReport(detailData);
            flex.refresh();
            this.closeModalYear();
        },
        closeModalYear () {
            this.yearModal = false;
        },
        openModalYear() {
            this.yearModal = true;
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
<style>
#fm-toolbar-wrapper #fm-toolbar > li > a span, #fm-toolbar-wrapper #fm-toolbar > .fm-toolbar-group-left > li > a span, #fm-toolbar-wrapper #fm-toolbar > .fm-toolbar-group-right > li > a span {
    left: -8px;
}
</style>
