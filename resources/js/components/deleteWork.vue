<template>
    <span>
        <button :class="`btn ${theme}`" v-on:click="show = true">
            <i class="fas fa-trash"></i> <slot></slot>
        </button>
        <modal :show="show" @close="show = false" col="col-md-10" height="90vh">
            <template slot="header">
                Agregar trabajadores 
                <i class="fas fa-arrow-right text-danger"></i>
                <span v-text="cronograma.planilla ? cronograma.planilla.descripcion : ''"></span>
                <i class="fas fa-arrow-right text-danger" v-if="cronograma.adicional"></i>
                <span v-if="cronograma.adicional">Adicional</span>
                <span v-if="cronograma.adicional" 
                    v-text="cronograma.numero" 
                    class="btn btn-sm btn-primary"
                ></span>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                    <div class="form-group">
                        <form class="row" v-on:submit="getWorks">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Buscar..." v-model="like">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-info">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <form class="table-response" :id="`delete-works-${count}`">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Seleccionar</th>
                                    <th>Nombre Completo</th>
                                    <th>N° Documento</th>
                                    <th>Categorias</th>
                                </tr>
                            </thead>
                            <tbody v-if="!loader">
                                <tr v-for="(history, w) in historial.data" :key="`history-delete-${w}`">
                                    <td>{{ w + 1 }}</td>
                                    <td>
                                        <input type="checkbox" name="historial[]" 
                                            v-on:change="validate"
                                            v-model="check_works"
                                            :value="history.id"
                                        >
                                    </td>
                                    <td class="uppercase">{{ history.work ? history.work.nombre_completo : '' }}</td>
                                    <td>{{ history.work ? history.work.numero_de_documento : '' }}</td>
                                    <td>
                                        <div class="row">
                                            <div class="btn btn-sm btn-block btn-danger">
                                                <span v-text="history.categoria ? history.categoria.nombre : '' "></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tr v-if="loader">
                                <td colspan="5" class="text-center">
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
    props: ['theme', "cronograma"],
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
    watch: {
        show(nuevo, old) {
            if (nuevo) {
                this.getWorks();
                this.count = 0;
            }else {
                this.check_works = [];
            }
        }
    },
    methods: {
        async getWorks(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;
            
            let api = unujobs("get", `/cronograma/${this.cronograma.id}/historial?query_search=${this.like}`);
            
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
            this.historial.data = [...this.historial.data, ...e.data];
        },
        async add(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;

            const form = new FormData(document.getElementById(`delete-works-${this.count}`));

            let api = unujobs("post", `/cronograma/${this.cronograma.id}/destroy-historial`, form);

            await api.then(async res => {

                let { status, message } = res.data;

                let icon = status ? "success" : "error";

                await notify({icon: icon, text: message});

                this.getWorks();

                this.check_works = [];

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

