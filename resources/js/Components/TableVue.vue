<template>
    <div>
        <div class="flex flex-col mt-5">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <input type="text" v-model="search" v-on:keyup="doTimerSearch" placeholder="Procurar...." class="form-input mt-1 mb-3 block w-full">
                    <div class="shadow overflow-hidden border-b border-gray-200">
                        <vuetable ref="vuetable"
                                  :css="css"
                                  :api-url="apiUrl"
                                  :append-params="appendParams"
                                  :fields="fields"
                                  data-path="data"
                                  pagination-path=""
                                  :per-page="perPage"
                                  noDataTemplate="Sem registros"
                                  @vuetable:pagination-data="onPaginationData"
                        >
                            <!-- slots -->
                            <template v-for="(slot, name) in $scopedSlots" :slot="name">
                                <slot :name="name"></slot>
                            </template>
                            <!-- scoped slots -->
                            <template v-for="(slot, name) in $scopedSlots" :slot="name" slot-scope="vuetableProps">
                                <slot :name="name" :row="vuetableProps.rowData"></slot>
                            </template>
                        </vuetable>
                    </div>
                    <div class="flex mt-2">
                        <div class="flex-1 pt-2">
                            Registros:
                            <select v-on:change="perPageHandle">
                                <option
                                    v-for="p in perPageOption"
                                    :value="p"
                                >{{ p }}</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <vuetable-pagination ref="pagination"
                                                 :css="cssPagination"
                                                 @vuetable-pagination:change-page="onChangePage"
                            ></vuetable-pagination>
                        </div>
                        <div class="flex-1 pt-2 text-right">
                            <vuetable-pagination-info
                                ref="paginationInfo"
                                info-template="Mostrando: {from} - {to} de {total} registros"
                                no-data-template="Sem informações"
                            ></vuetable-pagination-info>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Vuetable from "vuetable-2";
import VuetablePagination from 'vuetable-2/src/components/VuetablePagination';
import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo';

export default {
    name: 'TableVue',
    components: {
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
    },
    mounted() {
      console.log(this.$scopedSlots, this.$slots);
    },
    props: {
        refTable: {
            type: String,
            default () {
                return '';
            }
        },
        apiUrl: {
            type: String,
            default () {
                return '';
            }
        },
        perPageOption: {
            type: Array,
            default: function() { return [10,25,50,100,200] }
        },
        fields: {
            type: Array,
            required: true,
        },
        css: {
            type: Object,
            default () {
                return {
                    tableWrapper: '',
                    tableHeaderClass: 'bg-gray-600',
                    tableBodyClass: 'bg-white divide-y divide-gray-200',
                    tableClass: 'min-w-full divide-y divide-gray-200',
                    loadingClass: 'loading',
                    ascendingIcon: 'fa fa-chevron-up',
                    descendingIcon: 'fa fa-chevron-down',
                    ascendingClass: 'sorted-asc',
                    descendingClass: 'sorted-desc',
                    sortableIcon: 'fa fa-sort',
                    detailRowClass: 'vuetable-detail-row',
                    handleIcon: 'fa fa-bars text-secondary',
                    renderIcon(classes, options) {
                        return `<i class="${classes.join(' ')}"></span>`
                    }
                };
            }
        },
        appendParams: {
            type: Object,
            default () {
                return {};
            }
        },
    },
    data() {
        return {
            search: '',
            perPage: 10,
            data: [],
            cssPagination: {
                wrapperClass: '',
                activeClass: 'cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest mr-1 ml-1',
                disabledClass: 'disabled',
                pageClass: 'cursor-pointer inline-flex items-center px-4 py-2 bg-white border ml-1 mr-1 rounded-md text-xs font-semibold',
                linkClass: 'page-link',
                paginationClass: 'pagination',
                paginationInfoClass: 'float-left',
                dropdownClass: 'form-control',
                icons: {
                    first: 'fa fa-angle-double-left',
                    prev: 'fa fa-angle-left',
                    next: 'fa fa-angle-right',
                    last: 'fa fa-angle-double-right',
                }
            }
        };
    },
    methods: {
        refreshTable () {
            this.$nextTick(() => this.$refs.vuetable.refresh());
        },
        doFilter () {
            this.appendParams = {
                'search': this.search
            };
            this.$nextTick(() => this.$refs.vuetable.refresh());
        },
        doTimerSearch () {
            if (this.search.length) {
                clearTimeout(this.timer);
                this.timer = setTimeout(this.doFilter, 800);
            }
        },
        perPageHandle (value) {
            this.perPage = parseInt(value.target.value);
            this.$nextTick(() => this.$refs.vuetable.refresh());
        },
        onPaginationData(paginationData) {
            this.$refs.pagination.setPaginationData(paginationData)
            this.$refs.paginationInfo.setPaginationData(paginationData)
        },
        onChangePage(page) {
            this.$refs.vuetable.changePage(page);
        },
    }
}

</script>

<style scoped>

</style>
