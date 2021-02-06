<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Relatório Vencimento
            </h2>
        </template>

        <div class="py-5">
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <Pivot
                        ref="pivot"
                        toolbar
                        v-bind:ready="onReady"
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
import {Pivot} from "vue-flexmonster";
import 'flexmonster/flexmonster.css';

// Vue.use(Pivot);

export default {
    name: 'ReportsVencimento',
    components: {
        AppLayout,
        Pivot,
    },
    mounted() {
        console.log(this.$refs.pivot.flexmonster);
    },
    data() {
        return {
            graph: null,
        }
    },
    methods: {
        onReady: function () {
            //Connect Flexmonster to the data
            axios.get('/reports/json/28/vencimento')
                .then((response) => {
                    console.log(response);
                    this.$refs.pivot.flexmonster.updateData({ data: response.data });
                })
                .catch((error) => {
                    console.log(error)
                });
        },
    }
}
</script>
