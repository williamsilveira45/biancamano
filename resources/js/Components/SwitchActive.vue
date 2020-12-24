<template>
    <div class="flex items-center w-full">
        <!-- Toggle Button -->
        <label for="toogleA" class="flex items-center cursor-pointer" :for="'actives'+rowid">
            <!-- toggle -->
            <div class="relative">
                <!-- input -->
                <input id="toogleA" type="checkbox" class="hidden" :id="'actives'+rowid" :checked="this.active" @change="handleChange" />
                <!-- line -->
                <div class="toggle__line w-10 h-4 bg-gray-400 rounded-full shadow-inner"></div>
                <!-- dot -->
                <div class="toggle__dot absolute w-6 h-6 bg-white rounded-full shadow inset-y-0 left-0"></div>
            </div>
            <!-- label -->
            <div class="ml-3 text-gray-700 font-medium" v-html="getText"></div>
        </label>
    </div>
<!--    <div class="custom-control custom-switch">-->
<!--        <input type="checkbox" class="custom-control-input" @change="handleChange" :id="'actives'+rowid" :checked="this.activeProp">-->
<!--        <label class="custom-control-label" :for="'users'+rowid" v-html="getText"></label>-->
<!--    </div>-->
</template>

<script>
    export default {
        name: "SwitchActive",
        props: {
            active: {
                type: Boolean,
                required: true
            },
            endp: {
                type: String,
                required: true
            },
            rowid: {
                type: Number,
                required: true
            }
        },
        data() {
            return {
                text: '',
                activeData: false,
            };
        },
        computed: {
            getText() {
                return this.active ? 'Ativado' : 'Desativado';
            }
        },
        methods: {
            changingValue() {
                this.active = !this.active;
            },
            handleChange() {
                var self = this;
                axios.post(self.endp +'/'+ self.rowid, {
                    active: !self.active,
                })
                .then(response => {
                    if (response.data.success===true) {
                        this.changingValue();
                        Vue.$toast.success(response.data.message, {
                            duration: 3000,
                            position: 'top-right',
                            dismissible: true,
                        });
                    } else {
                        this.active = self.active;
                        Vue.$toast.error(response.data.message, {
                            duration: 3000,
                            position: 'top-right',
                            dismissible: true,
                        });
                    }
                })
                .catch(error => {
                    Vue.$toast.error(error.message, {
                        duration: 3000,
                        position: 'top-right',
                        dismissible: true,
                    });
                });
            },
    }
}
</script>

<style scoped>
.toggle__dot {
    top: -.25rem;
    left: -.25rem;
    transition: all 0.3s ease-in-out;
}

input:checked ~ .toggle__dot {
    transform: translateX(100%);
    background-color: #20a1ff;
}
</style>
