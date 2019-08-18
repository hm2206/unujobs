<template>
    <div class="btn-group">
        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        
        <modal @close="show = false" col="col-md-10" :show="show" height="95vh" style="padding-top: 10px;">
            <template slot="header">
                Situación laboral
                <i class="fas fa-arrow-right text-danger"></i> 
                <span v-text="nombre_completo"></span>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                
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
                        :param="param"
                        :send="send"
                        :infos="infos"
                        @get-cronograma="getCronograma"
                        @ready="setLoader"
                        :sindicatos="sindicatos"
                    >
                    </component>

                    <div class="col-md-12 mt-3" v-if="loader">
                        <div class="text-center">
                            <div class="spinner-border text-warning"></div>
                        </div>
                    </div>

                </div>

                <div :class="`card-footer ${send ? 'text-center' : 'text-right'}`" v-if="btn">
                    <button class="btn btn-primary"
                        v-if="!send"
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

import ConfigWork from './ConfigWork';
import ConfigWorkMas from './ConfigWorkMas';
import { setTimeout } from 'timers';

export default {
    components: {
        'work-config': ConfigWork,
        'work-config-mas': ConfigWorkMas
    },
    props: ["theme", 'param', "nombre_completo", "sindicatos"],
    data() {
        return {
            show: false,
            loader: false,
            current: 'work-config',
            items: [
                {id: 1, text: "Configuración", active: true, component: 'work-config', btn: true},
                {id: 2, text: "Más...", active: false, component: 'work-config-mas', btn: false},
            ],
            infos: [],
            dias: 30,
            numeros: [],
            numero:'',
            categoria_id: '',
            adicional: 0,
            cronograma: {},
            send: false,
            btn: true
        }
    },
    watch: {
        show() {
            if (this.categoria) {
                this.categoria_id = this.categoria;
            }
            this.getCargos();
        }
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
            setTimeout(function() {
                that.loader = false;
            }, 300);
        },
        async getCargos() {

            this.loader = true;
            let api = unujobs("get", `/work/${this.param}/info`);

            await api.then(res => {
                this.infos = res.data;

            }).catch(err => {
                console.log("algo salió mal");
            });

            this.loader = false;

        },
        btnPress(e) {
            this.send = true;
        },
        setLoader(e) {
            this.send = false;
        },
        getCronograma(e) {

            this.cronograma = e;
            this.dias = e.dias;
            this.adicional = e.adicional;

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
