<template>
</template>

<script>
import Vue from 'vue';
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

Vue.use(VueToast);

export default {
    name: "Notifications",
    data() {
        return {
            title: '',
            body: '',
            type: 'success',
            duration: 3,
        }
    },
    mounted() {
        let obj = this;
        Echo.channel("biancamano_capital_database_notification-received")
            .listen('.notifications', (e) => {
                obj.title = e.title;
                obj.body = e.body;
                obj.duration = e.duration;
                obj.type = e.type;
                obj.triggerEvent();
            });
    },
    methods: {
      triggerEvent() {
          Vue.$toast.[this.type](this.body, {
              duration: this.duration,
              position: 'top-right',
              dismissible: true,
              queue: false,
          });
      }
    },
}
</script>

<style scoped>

</style>
