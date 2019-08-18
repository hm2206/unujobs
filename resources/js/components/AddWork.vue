<template>
    <span>
        <button :class="`btn ${theme}`" v-on:click="show = true">
            <i class="fas fa-plus"></i> <slot></slot>
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

                    <form class="table-response" id="add-works">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Nombre Completo</th>
                                    <th>N° Documento</th>
                                    <th>Categorias</th>
                                </tr>
                            </thead>
                            <tbody v-if="!loader">
                                <tr v-for="(work, w) in work.data" :key="`work-${w}`">
                                    <td>
                                        <input type="checkbox" name="jobs[]" 
                                            v-on:change="validate"
                                            v-model="check_works"
                                            :value="work.id"
                                        >
                                    </td>
                                    <td>{{ work.nombre_completo }}</td>
                                    <td>{{ work.numero_de_documento }}</td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6" v-for="(info, i) in work.infos" :key="`info-${i}`">
                                                <div class="btn btn-sm btn-block btn-danger">
                                                    <span v-text="info.categoria ? info.categoria.nombre : ''"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tr v-if="loader">
                                <td colspan="4" class="text-center">
                                    <small class="spinner-border text-primary"></small>
                                </td>
                            </tr>
                            <tr v-if="!loader && work.total == 0">
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
                        :url="work.next_page_url"   
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
            work: {},
            count: 0,
            check_works: [],
            like: ""
        }
    },
    watch: {
        show(nuevo, old) {
            if (nuevo) {
                this.getWorks();
            }
        }
    },
    methods: {
        async getWorks(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;
            
            let api = unujobs("get", `/cronograma/${this.cronograma.id}/add?query_search=${this.like}`);
            
            await api.then(res => {
                this.work = res.data;
            }).catch(err => {

            });

            this.loader = false;
        },
        validate(e) {
            let { checked } = e.target;
            this.count = checked ? this.count + 1 : this.count - 1;
        },
        getData(e) {
            this.work.next_page_url = e.next;
            this.work.total = e.total;
            this.work.path = e.path;
            this.work.data = [...this.cronograma.data, ...e.data];
        },
        async add(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;

            const form = new FormData(document.getElementById('add-works'));

            let api = unujobs("post", `/cronograma/${this.cronograma.id}/add`, form);

            await api.then(async res => {

                let { status, message } = res.data;

                let icon = status ? "success" : "error";

                await notify({icon: icon, text: message});

                this.getWorks();

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

