<template>
    <div
        class="opacity-80 rounded-full h-24 w-24 flex items-center justify-center text-lg"
        style="background"
        :style="{background: randomBackground}"
    >
        <b>{{usernameCompacted}}</b>
    </div>
</template>

<script>
export default {
    name: "Avatar",
    props: {
        username: {
            type: String,
            required: true
        },
        background: {
            type: String,
            required: false,
        }
    },
    methods: {
        hasWhiteSpace (s) {
            return /\s/g.test(s);
        },
        hashCode (str) {
            var hash = 0;
            for (var i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            var colour = '#';
            for (var i = 0; i < 3; i++) {
                var value = (hash >> (i * 8)) & 0xFF;
                colour += ('00' + value.toString(16)).substr(-2);
            }
            return colour;
        },
        hexToRgbA (hex) {
            var c;
            if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
                c= hex.substring(1).split('');
                if(c.length== 3){
                    c= [c[0], c[0], c[1], c[1], c[2], c[2]];
                }
                c= '0x'+c.join('');
                return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+',.25)';
            }
        }
    },
    computed: {
        usernameCompacted () {
            let name = this.username;
            if (this.hasWhiteSpace(this.username)) {
                name = name.split(' ');
                name = name[0][0] + name[1][0];
            } else {
                name = name[0] + name[1];
            }

            return name.toUpperCase();
        },
        randomBackground () {
            if (this.background) {
                return this.background;
            }

            return this.hexToRgbA(this.hashCode(this.username));
        }
    }
}
</script>

<style scoped>

</style>
