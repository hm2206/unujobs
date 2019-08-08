<template>
    <div class="btn-group">
        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        
        <modal @close="show = false" col="col-md-10" :show="show">
            <template slot="header">
                Lista de boletas de 
                <i class="fas fa-arrow-right text-danger"></i> 
                <span v-text="nombre_completo"></span>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                    <form class="table-responsive" method="POST" :action="url"
                        v-on:submit="loader = true"
                    >
                        <input type="hidden" name="_token" :value="token">
                        <table class="table">
                            <thead class="text-primary">
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Planilla</th>
                                    <th>Observación</th>
                                    <th class="text-center">Adicional</th>
                                    <th class="text-center">Mes</th>
                                    <th class="text-center">Año</th>
                                </tr>
                             </thead>
                            <tbody v-if="!loader">
                                <tr v-for="(cronograma, c) in cronograma.data" :key="c">
                                    <td>
                                        <input type="checkbox" :value="cronograma.id" name="cronogramas[]" 
                                            v-on:change="validate"
                                        >
                                    </td>
                                    <td>{{ cronograma.planilla ? cronograma.planilla.descripcion : '' }}</td>
                                    <td>{{ cronograma.observacion }}</td>
                                    <td class="text-center">
                                        <span :class="`btn btn-sm btn-${cronograma.adicional ? 'primary' : 'danger'}`">
                                            {{ cronograma.adicional ? cronograma.numero : 'No' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="btn btn-sm btn-dark">
                                            {{ cronograma.mes }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="btn btn-sm btn-dark">
                                            {{ cronograma['año'] }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            <tr>
                                <td colspan="6" class="text-center" v-if="loader">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </td>
                                <td v-if="!loader && cronograma.total == 0" 
                                    class="text-center" colspan="6"
                                >
                                    <small>No hay registros disponibles, vuelva a recargar</small>
                                    <div>
                                        <button class="btn btn-sm btn-outline-dark"
                                            v-on:click="getBoletas"
                                        >
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                                
                        <button class="btn-fixed btn btn-success btn-circle btn-lg" 
                            :disabled="loader"
                            v-if="cronograma.total > 0 && count > 0"
                        >
                            <i class="fa fa-download"></i>
                        </button>

                    </form>
                </div>
                <div class="card-footer" v-if="!loader">
                    <btn-more :config="['btn-block']" 
                        :url="cronograma.next_page_url"   
                        @get-data="getData"
                    >
                    </btn-more>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>

import { unujobs } from '../services/api';

export default {
    props: ["theme", 'param', "url", "nombre_completo", "token"],
    data() {
        return {
            show: false,
            loader: true,
            cronograma: {},
            count: 0
        }
    },
    watch: {
        show(nuevo, old) {
            if (nuevo) {
                this.getBoletas();
            }
        }
    },
    methods: {
        async getBoletas(e) {
            
            if (e) {
                e.preventDefault();
            }

            this.loader = true;
            let api = unujobs("get", `/boleta/${this.param}`);
            await api.then(res => {
                this.cronograma = res.data;
            }).catch(err => {
                console.log(err);
            })

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
            this.cronograma.data = [...this.cronograma.data, ...e.data];
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
        bottom: 20px;
        right: 20px;
    }

    .min-height {
        height: 100%;
    }
</style>
