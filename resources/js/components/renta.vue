<template>
    <span>
        <modal :show="show" @close="show = false" col="col-md-7" height="90vh">
            <template slot="header">
                REPORTE DE RENTA <i class="fas fa-arrow-right text-danger"></i> <b class="text-primary">{{ info.work ? info.work.nombre_completo : '' }}</b>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                    <form class="table-response" :id="`add-cronogramas-${count}`">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Seleccionar</th>
                                    <th>Año</th>
                                    <th>Mes</th>
                                </tr>
                            </thead>
                            <tbody v-if="!loader">
                                <tr v-for="(cro, w) in cronograma.data" :key="`cronograma-add-${w}`">
                                    <td>{{ w + 1 }}</td>
                                    <td>
                                        <input type="checkbox" name="cronogramas[]" 
                                            v-on:change="validate"
                                            v-model="check_works"
                                            :value="cro.id"
                                        >
                                    </td>
                                    <td class="uppercase">
                                        <span class="btn btn-dark">{{ cro.año }}</span>
                                    </td>
                                    <td><span class="btn btn-dark">{{ cro.mes }}</span></td>
                                </tr>
                            </tbody>
                            <tr v-if="loader">
                                <td colspan="4" class="text-center">
                                    <small class="spinner-border text-primary"></small>
                                </td>
                            </tr>
                            <tr v-if="!loader && info.total == 0">
                                <td colspan="4" class="text-center">
                                    <small>No hay registros disponibles, vuelva a recargar</small>
                                    <div>
                                        <button class="btn btn-sm btn-outline-dark"
                                            v-on:click="getWorks"
                                        >
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <btn-validate 
                    :show="hasSelect"
                    @click="add"
                >
                    <i class="fas fa-save"></i>
                </btn-validate>

                <div class="card-footer text-center" v-if="!loader">
                    <btn-more :config="['btn-block']" 
                        :url="info.next_page_url"   
                        @get-data="getData"
                    >
                    </btn-more>
                </div>
            </template>
        </modal>
    </span>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['theme', 'info', 'vista'],
    data() {
        return {
            show: false,
            loader: false,
            cronograma: {
                data: []
            },
            count: 0,
            check_works: [],
            like: ""
        }
    },
    mounted() {
        this.show = this.vista;
    },
    watch: {
        show(nuevo, old) {
            if (nuevo) {
                this.getWorks();
                this.count = 0;
            }else {
                this.check_works = [];
                this.$emit('change-close');
            }
        },
        vista(nuevo) {
            this.show = nuevo;
        }
    },
    methods: {
        async getWorks(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;
            
            let api = unujobs("get", `/work/${this.info.id}`);
            
            await api.then(res => {
                this.cronograma = res.data;
            }).catch(err => {

            });

            this.loader = false;
        },
        validate(e) {
            let { checked } = e.target;
            this.count = checked ? this.count + 1 : this.count - 1;
        },
        getData(e) {
            this.cronograma.next_page_url = e.next;
            this.cronograma.total = e.total;
            this.cronograma.path = e.path;
            this.cronograma.data = [...this.info.data, ...e.data];
        },
        async add(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;

            const form = new FormData(document.getElementById(`add-cronogramas-${this.count}`));

            let api = unujobs("post", `/work/${this.info.id}/report`, form);

            await api.then(async res => {

                let { status, message } = res.data;

                let icon = status ? "success" : "error";

                await notify({icon: icon, text: message});

                this.getWorks();

                this.check_works = [];

                this.show = false;

            }).catch(err => {
                notify({icon: 'error', text: err.message});
            })

            this.loader = false;
        }
    },
    computed: {
        hasSelect() {
            return this.count > 0;
        }
    }
}
</script>


<style scoped>
    .p-relative {
        position: relative;
    }

    .btn-fixed {
        position: absolute;
        bottom: 70px;
        right: 40px;
    }

    .min-height {
        height: 100%;
    }
</style>

