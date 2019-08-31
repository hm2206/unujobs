<template>
    <div class="btn-group">
        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        
        <modal @close="show = false" col="col-md-10" :show="show" height="95vh" style="padding-top: 10px;">
            <template slot="header">
                Situación laboral
                <i class="fas fa-arrow-right text-danger"></i> 
                <span v-text="fullname" class="uppercase"></span>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">

                    <div class="col-md-12">
                        <div class="row align-items-center">

                            <div class="col-md-2">
                                <input type="text" :disabled="true" v-model="categoria.nombre" class="form-control">
                            </div>

                            <div class="col-md-2">
                                <input type="number" 
                                    :disabled="loader" name="mes" 
                                    v-model="tmp_cronograma.mes" min="1" max="12" 
                                    class="form-control"
                                    v-on:change="cambio"
                                >
                            </div>

                            <div class="col-md-2">
                                <input type="number" 
                                    :disabled="loader" 
                                    v-model="tmp_cronograma.año" name="year" 
                                    min="1999" 
                                    class="form-control"
                                    v-on:change="cambio"
                                >
                            </div>

                            <div class="col-md-2">
                                Adicional
                                <input type="checkbox" 
                                    v-model="adicional"
                                    v-on:change="cambio"
                                    :disabled="true"
                                >
                            </div>

                            <div class="col-md-2">
                                <input type="number" name="dias" v-model="tmp_cronograma.dias" disabled class="form-control">
                            </div>
                            
                            <div class="col-md-2" v-if="numeros.length > 0">
                                <select name="numero" :disabled="true" v-model="numero" class="form-control"
                                    v-on:change="cambio"
                                >
                                    <option v-for="(numero, nu) in numeros" :key="`numero-nu${nu}`" 
                                        :value="numero.numero"
                                    >
                                        {{ numero.numero }}
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <hr>
                
                    <ul class="nav nav-tabs">
                        <li class="nav-item" v-for="(item, i) in items" :key="`item-${i}`">
                            <a href="#" 
                                :class="`nav-link ${item.active ? 'active' : ''}`"
                                v-on:click="seleccionar($event, item)"
                            >
                                {{ item.text }}
                            </a>
                        </li>
                    </ul>

                    <component :is="current" v-if="!loader" 
                        :categoria="categoria_id"
                        :param="job_current"
                        :mes="tmp_cronograma.mes"
                        :year="tmp_cronograma.año"
                        :adicional="adicional"
                        :numero="numero"
                        :send="send"
                        :info="info"
                        :work="work"
                        :objetos="objetos"
                        :tmp_cronograma="tmp_cronograma"
                        @get-cronograma="getCronograma"
                        @get-numeros="getNumeros"
                        @ready="setLoader"
                    >
                    </component>

                    <div class="col-md-12 mt-3" v-if="loader">
                        <div class="text-center">
                            <div class="spinner-border text-warning"></div>
                        </div>
                    </div>

                </div>

                <div :class="`card-footer ${send ? 'text-center' : 'text-right'}`">

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
import { setTimeout } from 'timers';

export default {
    components: {
        'work-general': WorkGeneral,
        'work-afectacion': WorkAfectacion,
        'work-remuneracion': WorkRemuneracion,
        'work-descuento': WorkDescuento,
        'work-obligacion': WorkObligacion,
        'work-detalle': WorkDetalle
    },
    props: [
        "theme", 'param', "tmp_info", "nombre_completo", 
        "paginate", 'planilla_id', 'tmp_adicional', 'tmp_numero', "cronograma"
    ],
    data() {
        return {
            show: false,
            loader: false,
            current: 'work-general',
            job_current: '',
            fullname: '',
            isCategoria: false,
            categoria: {},
            work: {},
            objetos: {
                bancos: [],
                afps: []
            },
            block: false,
            items: [
                {id: 1, text: "Datos Generales", active: true, component: 'work-general', btn: true},
                {id: 2, text: "Afectación Presupuestal", active: false, component: 'work-afectacion', btn: true},
                {id: 3, text: "Remuneraciones", active: false, component: 'work-remuneracion', btn: true},
                {id: 4, text: "Descuentos", active: false, component: 'work-descuento', btn: true},
                {id: 5, text: "Obligaciones Judiciales", active: false,  component: 'work-obligacion', btn: false},
                {id: 6, text: "Más...", active: false,  component: 'work-detalle', btn: false}
            ],
            info: {},
            dias: 30,
            numeros: [],
            numero: 1,
            categoria_id: '',
            adicional: false,
            tmp_cronograma: {},
            send: false,
            btn: true
        }
    },
    mounted() {
        this.tmp_cronograma = this.cronograma;
        this.info = this.tmp_info; 
        this.categoria = this.info.categoria;  
        this.job_current = this.param;
        this.fullname = this.nombre_completo;
        this.adicional = this.tmp_adicional == 1 ? true : false;
        this.isCategoria = this.categoria ? true : false;
        this.numero = this.tmp_numero;
    },
    watch: {
        show() {
            if (this.categoria) {
                this.categoria_id = this.categoria;
                this.getCargos();
            }
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
        cambio(e) {
            let that = this;
            this.loader = true;
            setTimeout(function() {
                that.loader = false;
            }, 300);
        },
        cambioCategoria(e) {         
            let that = this;
            let id = e.value;
            this.loader = true;
            this.infos.filter((inf) => {
                if (inf.id == id) {
                    this.info = inf;
                }
            });
            setTimeout(function() {
                that.loader = false;
            }, 300);
        },
        async getCargos() {

            this.loader = true;
            let api = unujobs("get", `/work/${this.job_current}/info`);

            await api.then(res => {
                let { infos, work, bancos, afps } = res.data;
                this.infos = infos;
                this.work = work;
                this.objetos.bancos = bancos;
                this.objetos.afps = afps;
                this.fullname = work.nombre_completo;

                for(let info of this.infos) {
                    
                    if (!this.isCategoria) {
                        
                        if (this.planilla_id == info.planilla_id) {
                            this.categoria_id = info.categoria_id;
                            this.info = info;
                            break;
                        }

                    }else if(this.categoria_id == info.categoria_id) {
                        this.info = info;
                        break;

                    }

                }

            }).catch(err => {
                console.log("algo salió mal");
            });

            this.loader = false;
            this.block = false;

        },
        btnPress(e) {
            this.send = true;
        },
        setLoader(e) {
            this.send = false;
        },
        getCronograma(e) {

            this.tmp_cronograma = e;
            this.dias = e.dias;
            this.block = false;

        },
        getNumeros(e) {

            this.numeros = e;

        },
        next(e) {
            if (this.paginate.length > this.findCurrent + 1) {
                this.isCategoria = false;   
                this.block = true;
                this.job_current = this.paginate[this.findCurrent + 1];
                this.getCargos();
            }else {
                alert("No hay mas trabajadores");
            }
        },
        prev(e) {
            if (this.findCurrent >= 1) {
                this.isCategoria = false;   
                this.block = true;
                this.job_current = this.paginate[this.findCurrent - 1];
                this.getCargos();
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
</style>
