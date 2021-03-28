<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ customer.name }} - Relatórios
            </h2>
        </template>

        <div class="py-5">
            <div class="w-full mx-auto sm:px-6" style="z-index: 1000">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <div v-for="(report, index) in reports">
                        <h1 class="text-2xl mb-2 border-b-2 border-fuchsia-200 capitalize">{{ index }}</h1>
                        <div class="grid grid-cols-4 gap-5">
                            <a
                                class="p-2 cursor-pointer flex justify-center hover:bg-gray-100 rounded-sm"
                                v-for="(reportType, indexType) in report"
                                :href="`/reports/${customer.id}/${index}/${indexType}/`"
                            >
                                <div class="text-center">
                                    <font-awesome-icon
                                        :icon="reportType.icon"
                                        style="font-size: 2.5em;"
                                        :style="{ color: reportType.color }"
                                    />
                                    <p class="text-center mt-3">{{reportType.name}}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout'

export default {
    name: 'Reports',
    components: {
        AppLayout,
    },
    props: ['customer'],
    data() {
        return {
            reports: {
                receitas: {
                    vencimento: {
                        icon: 'file-invoice-dollar',
                        name: 'Vencimento',
                        color: '#FBBF24'
                    },
                    competencia: {
                        icon: 'file-invoice',
                        name: 'Competência',
                        color: '#6EE7B7'
                    },
                    credito: {
                        icon: 'money-bill-wave',
                        name: 'Crédito',
                        color: '#60A5FA'
                    }
                },
                despesas: {
                    vencimento: {
                        icon: 'file-invoice-dollar',
                        name: 'Vencimento',
                        color: '#FBBF24'
                    },
                    credito: {
                        icon: 'money-bill-wave',
                        name: 'Baixa',
                        color: '#60A5FA'
                    }
                }
            },
        }
    },
}
</script>
