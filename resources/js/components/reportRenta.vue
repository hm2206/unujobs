<template>
    <span>
        <button :class="`btn ${theme}`" v-on:click="show = true">
            <i class="fas fa-file"></i> <slot></slot>
        </button>
        <modal :show="show" @close="show = false" col="col-md-10" index="8" height="90vh">
            <template slot="header">
                Generar reporte para renta
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

                    <div class="table-response">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre Completo</th>
                                    <th>N° Documento</th>
                                    <th>Categorias</th>
                                    <th class="text-center">Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody v-if="!loader">
                                <tr v-for="(info, w) in info.data" :key="`info-report-renta-${w}`">
                                    <td>{{ w + 1 }}</td>
                                    <td class="uppercase">{{ info.work ? info.work.nombre_completo : '' }}</td>
                                    <td>{{ info.work ? info.work.numero_de_documento : '' }}</td>
                                    <td>
                                        <div class="row">
                                            <div class="btn btn-sm btn-block btn-danger">
                                                <span v-text="info.categoria ? info.categoria.nombre : '' "></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-success"
                                            v-on:click="seleccion(info)"
                                        >
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
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
                    </div>
                </div>

                <div class="card-footer text-center" v-if="!loader">
                    <btn-more :config="['btn-block']" 
                        :url="info.next_page_url"   
                        @get-data="getData"
                    >
                    </btn-more>
                </div>

                <renta :vista="vista" 
                    :info="param"
                    @change-close="changeClose"
                />
            </template>
        </modal>
    </span>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';
import renta from './renta';

export default {
    components: {
        renta
    },
    props: ['theme'],
    data() {
        return {
            show: false,
            loader: false,
            vista: false,
            param: {},
            info: {
                data: []
            },
            like: "",
            titulo: ""
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
        changeClose(e) {
            this.vista = false;
        },
        seleccion(info) {
            this.vista = true;
            this.param = info;
        },
        async getWorks(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;
            
            let api = unujobs("get", `/listar/work?query_search=${this.like}`);
            
            await api.then(res => {
                this.info = res.data;
            }).catch(err => {

            });

            this.loader = false;
        },
        getData(e) {
            this.info.next_page_url = e.next;
            this.info.total = e.total;
            this.info.path = e.path;
            this.info.data = [...this.info.data, ...e.data];
        },
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

