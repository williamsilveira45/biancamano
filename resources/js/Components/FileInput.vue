<template>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <select v-model="customer_id" name="customer_id" class="form-control">
                    <option value="0" selected="selected">Selecione um Cliente</option>
                    <option v-for="(item, index) in customers" :value="index">{{ item }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <file-upload
                    ref="upload"
                    v-model="files"
                    post-action="/files/upload"
                    :data="{customer_id: customer_id}"
                    :headers="headers"
                    @input-file="inputFile"
                    @input-filter="inputFilter"
                >
                    <a href="#" class="btn btn-info">Selecionar Arquivo</a>
                </file-upload>
            </div>
            <div class="col-md-2">
                <button class="btn btn-success" v-show="!$refs.upload || !$refs.upload.active" @click.prevent="$refs.upload.active = true" type="button">Começar upload</button>
                <button class="btn btn-warning" v-show="$refs.upload && $refs.upload.active" @click.prevent="$refs.upload.active = false" type="button">Cancelar upload</button>
            </div>
        </div>

        <div class="mt-4" v-if="files.length > 0">

            <table class="border-collapse w-full">
                <thead>
                    <tr>
                        <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">Nome</th>
                        <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">Tamanho</th>
                        <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">Velocidade</th>
                        <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">Progresso</th>
                        <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white lg:hover:bg-gray-100 flex lg:table-row flex-row lg:flex-row flex-wrap lg:flex-no-wrap mb-10 lg:mb-0" v-for="file in files">
                        <td class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b block lg:table-cell relative lg:static">{{file.name}}</td>
                        <td class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b block lg:table-cell relative lg:static">{{bytesToSize(file.size)}}</td>
                        <td class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b block lg:table-cell relative lg:static">{{bytesToSize(file.speed)}} /s</td>
                        <td class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b block lg:table-cell relative lg:static"><progress-bar text-position="top" :text="file.progress+'%'" :val="file.progress"></progress-bar></td>
                        <td class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b block lg:table-cell relative lg:static">
                            <font-awesome-icon v-if="file.success" :style="{ color: '#1F9E46' }" icon="check-circle"  />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import FileUpload from 'vue-upload-component/src'
import swal from "sweetalert";
import ProgressBar from 'vue-simple-progress';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default {
    name: "FileInput",
    props: {
        csrf: {
            type: String,
            required: true
        },
        customers: {
            type: Object,
            required: true
        },
    },
    data: function () {
        return {
            files: [],
            headers: {
                'X-Csrf-Token': this.csrf,
            },
            customer_id: 0,
        }
    },
    components: {
        FileUpload,
        ProgressBar,
        FontAwesomeIcon,
    },
    mounted() {
        console.log(this.$refs);
    },
    methods: {
        /**
         * Has changed
         * @param  Object|undefined   newFile   Read only
         * @param  Object|undefined   oldFile   Read only
         * @return undefined
         */
        inputFile: function (newFile, oldFile) {
            if (newFile && oldFile && !newFile.active && oldFile.active) {
                // Get response data
                if (newFile.response.success) {
                    swal("Sucesso!", newFile.response.message, "success");
                    this.$parent.atualizarTabela();
                } else {
                    swal("Erro", newFile.response.message, "error");
                }

                if (newFile.xhr) {
                    //  Get the response status code
                    console.log('status', newFile.xhr.status)
                }
            }
        },
        /**
         * Pretreatment
         * @param  Object|undefined   newFile   Read and write
         * @param  Object|undefined   oldFile   Read only
         * @param  Function           prevent   Prevent changing
         * @return undefined
         */
        inputFilter: function (newFile, oldFile, prevent) {
            if (this.customer_id < 1) {
                swal("Erro", 'Você precisa selecionar um cliente antes', "error");
                return prevent()
            }

            if (newFile && !oldFile) {
                // Filter non-image file
                if (!/\.(csv)$/i.test(newFile.name)) {
                    return prevent()
                }
            }

            // Create a blob field
            newFile.blob = ''
            let URL = window.URL || window.webkitURL
            if (URL && URL.createObjectURL) {
                newFile.blob = URL.createObjectURL(newFile.file)
            }
        },
        bytesToSize(bytes) {
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            if (bytes === 0) {
                return 'n/a';
            }
            const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10);
            if (i === 0) {
                return `${bytes} ${sizes[i]})`;
            }
            return `${(bytes / 1024 ** i).toFixed(1)} ${sizes[i]}`;
        }
    }
}
</script>

<style scoped>

</style>
