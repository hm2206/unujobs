<template>
    <div class="btn-group">
        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        
        <modal @close="show = false" col="col-md-10" :show="show" height="90vh">
            <template slot="header">
                Lista de boletas de 
                <i class="fas fa-arrow-right text-danger"></i> 
                <span v-text="nombre_completo"></span>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                    <form class="table-responsive" method="POST" :action="url" id="generate-boletas"
                        v-on:submit="generate"
                    >
                        <input type="hidden" name="_token" :value="token"/>
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
                                <tr v-for="(history, c) in historial.data" :key="c">
                                    <td>
                                        <input type="checkbox" :value="history.id" name="historial[]" 
                                            v-on:change="validate"
                                            v-model="check_cronogramas"
                                        >
                                    </td>
                                    <td>{{ history.planilla ? history.planilla.descripcion : '' }}</td>
                                    <td>{{ history.observacion }}</td>
                                    <td class="text-center">
                                        <span :class="`btn btn-sm btn-${history.cronograma.adicional ? 'primary' : 'danger'}`">
                                            {{ history.cronograma && history.cronograma.adicional ? 
                                                history.cronograma.numero : 'No' 
                                            }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="btn btn-sm btn-dark">
                                            {{ history.cronograma ? history.cronograma.mes : '' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="btn btn-sm btn-dark">
                                            {{ history.cronograma ? history.cronograma.año : '' }}
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
                                <td v-if="!loader && historial.total == 0" 
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
                            v-if="historial.total > 0 && count > 0"
                        >
                            <i class="fa fa-download"></i>
                        </button>

                    </form>
                </div>
                <div class="card-footer" v-if="!loader">
                    <btn-more :config="['btn-block']" 
                        :url="historial.next_page_url"   
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
import notify from 'sweetalert';

export default {
    props: ["theme", 'param', "url", "nombre_completo", "token"],
    data() {
        return {
            show: false,
            loader: true,
            historial: {},
            count: 0,
            check_cronogramas: []
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
            let api = unujobs("get", `/info/${this.param}/historial`);
            await api.then(res => {
                this.historial = res.data;
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
            this.historial.next_page_url = e.next;
            this.historial.total = e.total;
            this.historial.path = e.path;
            this.hsitorial.data = [...this.historial.data, ...e.data];
        },
        async generate(e) {

            e.preventDefault();
            this.loader = true

            const form = new FormData(document.getElementById('generate-boletas'));

            let api = unujobs("post", `/boleta/${this.param}`, form);

            await api.then(res => {
                notify({icon: 'success', text: 'Las boletas están siendo procesadas. Nosotros le notificaremos'});
                this.check_cronogramas = [];
            }).catch(err => {
                notify({icon: 'error', text: 'Ocurrio un error al procesar la solicitud'});
            });

            this.loader = false;
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
