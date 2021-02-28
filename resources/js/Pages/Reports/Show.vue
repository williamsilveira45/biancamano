<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Selecionar Cliente
            </h2>
        </template>

        <div class="py-5">
            <div class="w-full mx-auto sm:px-6" style="z-index: 1000">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <div class="grid grid-cols-4 gap-5">
                        <a :href="`/reports/${index}/`" class="p-2 cursor-pointer flex justify-center hover:bg-gray-100 rounded-sm" v-for="(customer, index) in customers">
                            <div>
                                <avatar :username="customer"></avatar>
                                <p class="text-center mt-3">{{customer}}</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout'
import Avatar from "@/Components/Avatar";

export default {
    name: 'SelectCustomer',
    components: {
        AppLayout,
        Avatar,
    },
    props: ['customers'],
}
</script>
