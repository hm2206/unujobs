<template>
    <div class="btn-group">
        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        
        <modal @close="show = false" col="col-md-10" :show="show" height="95vh" style="padding-top: 10px;">
            <template slot="header">
                Situación laboral
                <i class="fas fa-arrow-right text-danger"></i> 
                <span v-text="history.work ? history.work.nombre_completo : ''" class="uppercase"></span>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">

                    <div class="col-md-12">
                        <div class="row align-items-center">

                            <div class="col-md-2">
                                <div class="form-control">
                                    {{ history.categoria ? history.categoria.nombre : '' }}
                                </div>
                            </div>

                            <div class="col-md-2">
                                <input type="number" 
                                    :disabled="loader" name="mes" 
                                    v-model="tmp_cronograma.mes" min="1" max="12" 
                                    class="form-control"
                                >
                            </div>

                            <div class="col-md-2">
                                <input type="number" 
                                    :disabled="loader" 
                                    v-model="tmp_cronograma.año" name="year" 
                                    min="2019" 
                                    class="form-control"
                                >
                            </div>

                            <div class="col-md-2">
                                Adicional
                                <input type="checkbox" 
                                    v-model="tmp_cronograma.adicional"
                                    :disabled="loader"
                                >
                            </div>

                            <div class="col-md-2">
                                <input type="number" name="dias" disabled class="form-control">
                            </div>
                            
                            <div class="col-md-2" v-if="false">
                                <input type="text" class="form-control" :disabled="true">
                            </div>

                        </div>
                    </div>

                    <hr>
                
                    <ul class="nav nav-tabs">
                        <li class="nav-item" v-for="(item, i) in items" :key="`item-${i}`">
                            <a href="#" 
                                :class="`nav-link text-sm ${item.active ? 'active' : ''}`"
                                v-on:click="seleccionar($event, item)"
                            >
                                {{ item.text }}
                            </a>
                        </li>
                    </ul>
                    
                    <component :is="current" v-if="!loader"
                        :param="job_current"
                        :send="send"
                        :history="history"
                        :cronograma="tmp_cronograma"
                        @ready="setLoader"
                    >
                    </component>

                    <div class="col-md-12 mt-3" v-if="loader">
                        <div class="text-center">
                            <div class="spinner-border text-warning"></div>
                        </div>
                    </div>

                </div>

                <div :class="`card-footer`">
                    <div class="row justify-content-between">
                        <div class="col-md-6">
                            <buscar-info theme="btn-info" :leave="clear" @find="getFind"
                                :planilla_id="tmp_cronograma.planilla_id"
                            >
                                <i class="fas fa-search"></i> Buscar
                            </buscar-info>
                            <button class="btn btn-danger" v-if="search" v-on:click="limpiarBusqueda">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-dark"
                                v-on:click="prev"
                                :disabled="block"
                                v-if="!send"
                            >
                                <i class="fas fa-arrow-left"></i>
                            </button>

                            <button class="btn btn-dark"
                                v-on:click="next"
                                :disabled="block"
                                v-if="!send"
                            >
                                <i class="fas fa-arrow-right"></i>
                            </button>

                            <button class="btn btn-primary"
                                v-if="!send  && btn"
                                v-on:click="btnPress"
                            >
                                    <i class="fa fa-sync"></i> Actualizar
                            </button>

                            <div class="text-primary spinner-border" v-if="send"></div>
                        </div>
                    </div>
                </div>

            </template>
        </modal>
    </div>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

import WorkGeneral from './WorkGeneral';
import WorkRemuneracion from './WorkRemuneracion';
import WorkDescuento from './WorkDescuento';
import WorkObligacion from './WorkObligacion';
import WorkDetalle from './WorkDetalle';
import WorkAfectacion from './WorkAfectacion';
import BuscarInfo from './BuscarInfo';
import { setTimeout } from 'timers';

export default {
    components: {
        'work-general': WorkGeneral,
        'work-afectacion': WorkAfectacion,
        'work-remuneracion': WorkRemuneracion,
        'work-descuento': WorkDescuento,
        'work-obligacion': WorkObligacion,
        'work-detalle': WorkDetalle,
        'buscar-info': BuscarInfo
    },
    props: [
        "theme", 'param', "historial", "nombre_completo", 
        "paginate", "cronograma"
    ],
    data() {
        return {
            show: false,
            loader: false,
            current: 'work-general',
            job_current: '',
            fullname: '',
            block: false,
            items: [
                {id: 1, text: "Datos Generales", active: true, component: 'work-general', btn: true},
                {id: 2, text: "Afectación Presupuestal", active: false, component: 'work-afectacion', btn: true},
                {id: 3, text: "Remuneraciones", active: false, component: 'work-remuneracion', btn: true},
                {id: 4, text: "Descuentos", active: false, component: 'work-descuento', btn: true},
                {id: 6, text: "Tasa Educacionales", active: false,  component: 'work-detalle', btn: true},
                {id: 5, text: "Obligaciones Judiciales", active: false,  component: 'work-obligacion', btn: false},
            ],
            history: {},
            tmp_cronograma: {
                dias: 30,
                numero: 1,
                adicional: 0
            },
            send: false,
            btn: true,
            search: false,
            clear: false
        }
    },
    mounted() {
        this.tmp_cronograma = this.cronograma;
        this.history = this.historial; 
        this.job_current = this.param;
        this.fullname = this.nombre_completo;
    },
    watch: {
        show() {
            this.job_current = this.history.id;
            this.history = this.historial;
        },
    },

    methods: {
        async seleccionar(e, item) {
            e.preventDefault();
            if (!this.send) {
                this.loader = true;
                await this.items.map((i) => {
                    if (item.id == i.id) {
                        i.active = true;
                    }else {
                        i.active = false;
                    }
                });

                this.loader = false;
                this.current = item.component;
                this.btn = item.btn;
            }
        },
        async cambio(e) {

            this.loader = true;
            this.loader = false;

        },
        async getHistory() {
            this.loader = true;
            let api = unujobs('get', `/historial/${this.job_current}`);
            api.then(res => {
                this.history = res.data;
            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal'});
            });
            this.block = false;
            this.loader = false;
        },
        btnPress(e) {
            this.send = true;
        },
        setLoader(e) {
            this.send = false;
        },
        async getFind(e) {
            this.block = true;
            this.job_current = e;
            this.search = true;
            // await this.getCargos();
            this.block = true;
        },
        limpiarBusqueda(e) {
            this.block = true;
            this.search = false;
            this.clear = true;
            this.job_current = this.param;
            // this.getCargos();
        },
        next(e) {
            if (this.paginate.length > this.findCurrent + 1) {
                this.block = true;
                this.job_current = this.paginate[this.findCurrent + 1];
                this.getHistory();
            }else {
                alert("No hay mas trabajadores");
            }
        },
        prev(e) {
            if (this.findCurrent >= 1) { 
                this.block = true;
                this.job_current = this.paginate[this.findCurrent - 1];
                this.getHistory();
            }else {
                alert("No hay mas trabajadores");
            }
        }
    },
    computed: {
        findCurrent() {
            let index = 0;
            for (let pag of this.paginate) {
                if (pag == this.job_current) {
                    return index;
                }
                index++;
            }
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

    .form-control {
        font-size: 14px !important;
        font-weight: bold;
    }

    .text-sm {
        font-size: 14px;
    }

</style>
