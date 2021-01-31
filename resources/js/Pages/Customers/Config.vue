<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <a href="#" style="font-size: 12px;color: #20a1ff;" @click.prevent="() => backPage()">< Voltar</a>
                <br>
                {{ $page.customer.name }} - <span style="font-size: 14px; color: #a0aec0;">Configurações</span>
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert" v-if="successMsg.length">
                    <div class="flex">
                        <div>
                            <p class="text-sm">{{ successMsg }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <nav class="tabs flex flex-col sm:flex-row">
                        <button
                            v-for="(tab, index) in tabs"
                            :data-target="'panel-'+index"
                            v-bind:class="[index === currentTab ? activeTab : cssTab]"
                            @click.prevent="changeTab(index)"
                        >
                            {{tab}}
                        </button>
                    </nav>

                    <div id="panels">
                        <div v-for="(tab, index) in tabContent" class="tab-content py-5" v-bind:class="[index === currentTab ? 'active' : null]">
                            <component :is="tab" :customer="$page.customer" :plano_contas="$page.plano_contas"></component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import moment from 'moment';
import JetButton from "@/Jetstream/Button";
import JetDangerButton from "@/Jetstream/DangerButton";
import Swal from 'sweetalert2'
import Vue from "vue";
import PlanoDeContas from "@/Pages/Customers/PlanoDeContas";

export default {
    components: {
        AppLayout,
        JetSecondaryButton,
        JetButton,
        JetDangerButton,
        PlanoDeContas
    },
    data() {
        return {
            successMsg: '',
            tabs: [
                'Plano de Contas',
            ],
            tabContent: [
                'PlanoDeContas',
            ],
            currentTab: 0,
            cssTab: 'tab ext-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none',
            activeTab: 'tab active text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500',
        }
    },
    methods: {
        backPage() {
            window.history.back();
        },
        changeTab(index) {
            this.currentTab = index;
        }
    }
}
</script>
