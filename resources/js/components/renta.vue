<template>
    <span>
        <modal :show="show" @close="show = false" col="col-md-7" height="90vh">
            <template slot="header">
                REPORTE DE RENTA <i class="fas fa-arrow-right text-danger"></i> <b class="text-primary">{{ work.nombre_completo }}</b>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                    <form class="table-response" :id="`add-cronogramas-${count}`">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Seleccionar</th>
                                    <th>Categoria</th>
                                    <th>Año</th>
                                    <th>Mes</th>
                                </tr>
                            </thead>
                            <tbody v-if="!loader">
                                <tr v-for="(history, w) in historial.data" :key="`historial-add-${w}`">
                                    <td>{{ w + 1 }}</td>
                                    <td>
                                        <input type="checkbox" name="historial[]" 
                                            v-on:change="validate"
                                            v-model="check_works"
                                            :value="history.id"
                                        >
                                    </td>
                                    <td class="uppercase">
                                        <span class="btn btn-sm btn-danger">{{ history.categoria ? history.categoria.nombre : '' }}</span>
                                    </td>
                                    <td class="uppercase">
                                        <span class="btn btn-sm btn-dark">{{ history.cronograma ? history.cronograma.año : '' }}</span>
                                    </td>
                                    <td><span class="btn btn-sm btn-dark">{{ history.cronograma ? history.cronograma.mes : '' }}</span></td>
                                </tr>
                            </tbody>
                            <tr v-if="loader">
                                <td colspan="4" class="text-center">
                                    <small class="spinner-border text-primary"></small>
                                </td>
                            </tr>
                            <tr v-if="!loader && historial.total == 0">
                                <td colspan="5" class="text-center">
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
                        :url="historial.next_page_url"   
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
    props: ['theme', 'work', 'vista'],
    data() {
        return {
            show: false,
            loader: false,
            historial: {
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
            
            let api = unujobs("get", `/work/${this.work.id}/historial`);
            
            await api.then(res => {
                this.historial = res.data;
            }).catch(err => {

            });

            this.loader = false;
        },
        validate(e) {
            let { checked } = e.target;
            this.count = checked ? this.count + 1 : this.count - 1;
        },
        getData(e) {
            this.historial.next_page_url = e.next;
            this.historial.total = e.total;
            this.historial.path = e.path;
            this.historial.data = [...this.info.data, ...e.data];
        },
        async add(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;

            const form = new FormData(document.getElementById(`add-cronogramas-${this.count}`));

            let api = unujobs("post", `/work/${this.work.id}/report`, form);

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

