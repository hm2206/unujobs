<template>
    <div class="card-body">

        <form class="row" id="form-detalle" v-on:submit="saveDetalle"
            v-if="show"
        >
            <div class="col-md-4">
                <select name="type_descuento_id" class="form-control">
                    <option value="">Seleccionar...</option>
                    <option :value="descuento.id"  v-for="(descuento, desc) in type_descuentos" :key="desc">
                        {{ descuento.descripcion }}
                    </option>
                </select>
                <small class="text-danger">{{ errors.type_descuento_id ? errors.type_descuento_id[0] : '' }}</small>
            </div>
            <div class="col-md-4">
                <input type="text" name="descripcion" class="form-control" placeholder="Descripción">
                <small class="text-danger">{{ errors.descripcion ? errors.descripcion[0] : '' }}</small>
            </div>
            <div class="col-md-2">
                <button class="btn btn-success"
                    v-if="!loader"
                >
                    <i class="fas fa-save"></i> Guardar
                </button>
                <div class="text-center" v-if="loader">
                    <div class="spinner-border"></div>
                </div>
            </div>
            <div class="col-md-12">
                <hr>
            </div>
        </form>

        <div class="mt-4 row justify-content-between" v-if="show">
            <div class="col-md-12">
                <div class="row">
                    <form :id="`form-detalles-${ty}`" class="col-md-6" v-for="(type, ty) in type_detalles" :key="ty"
                        v-on:submit="submit($event, ty)"
                    >
                        <div class="row align-items-center mb-1">
                            <div class="col-md-7 text-right">
                                <span class="text-primary">{{ type.type_descuento ? type.type_descuento.descripcion : '' }}</span>
                                <i class="fas fa-arrow-left text-danger"></i>
                                <span class="text-dark">{{ type.descripcion }}</span>
                                <input type="hidden" name="type_descuento_id" :value="type.type_descuento_id"/>
                                <input type="hidden" name="type_detalle_id" :value="type.id"/>
                                <input type="hidden" name="cronograma_id" :value="cronograma.id">
                                <input type="hidden" name="categoria_id" :value="categoria">
                                <input type="hidden" name="work_id" :value="param">
                            </div>
                            <div class="col-md-3">
                                <input type="number" v-model="type.monto" step="any" 
                                    class="form-control" 
                                    name="monto"
                                    :disabled="loader"
                                >
                            </div>
                            <div class="col-md-2">
                                <button class="btn-success btn btn-sm"
                                    :disabled="loader"
                                >
                                    <div class="fas fa-save"></div>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-100" v-if="!show">
            <div class="text-center">
                No hay registros disponibles!
            </div>
        </div>
    
    </div>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['categoria', 'mes', 'year', 'adicional', 'numero', 'param', 'send', 'info'],
    data() {
        return {
            type_descuentos: [],
            type_detalles: [],
            detalles: [],
            loader: false,
            show: true,
            cronograma: {},
            observacion: '',
            errors: {}
        };
    },
    mounted() {
        this.getTypeDescuentos();
        this.getTypeDetalles();
        this.getDetalles();
    },
    methods: {
        getTypeDetalles() {
            let api = unujobs('get', '/type_detalle');
            api.then(res => {
                this.type_detalles = res.data;
            }).catch(err => {

            });
        },
        getTypeDescuentos() {
            let api = unujobs('get', '/type_descuento');
            api.then(res => {
                this.type_descuentos = res.data;
            }).catch(err => {

            });
        },
        async saveDetalle(e) {
            e.preventDefault();
            this.loader = true;
            const form = new FormData(document.getElementById('form-detalle'));
            let api = unujobs('post', '/type_detalle', form);
            await api.then(res => {
                let { status, message, body } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});

                if (body) {
                    this.type_detalles.push(body);
                }

            }).catch(err => {
                let { data } = err.response;
                if (data.errors) {
                    this.errors = data.errors;
                }else {
                    notify({icon: 'error', text: 'Algo salió mal'});
                }
            });
            this.loader = false;
        },
        getDetalles() {

            let adicional = this.adicional ? 1 : 0;

            let  api = unujobs(
                'get',
                `/work/${this.param}/detalle?mes=${this.mes}&year=${this.year}&adicional=${adicional}&categoria_id=${this.categoria}&numero=${this.numero}`
            );

            api.then(res => {

                let { detalles, cronograma, numeros } = res.data;
                this.detalles = detalles;
                this.cronograma = cronograma;
                this.$emit('get-numeros', numeros);
                this.$emit('get-cronograma', cronograma);
                this.show = true;

                for(let type of this.type_detalles) {
                    for(let detalle of this.detalles) {
                        if (detalle.type_detalle_id == type.id) {
                            type.monto = detalle.monto;
                            break;
                        }else {
                            type.monto = 0;
                        }
                    }
                }


            }).catch(err => {

                console.log("algo salió mal");
                this.show = false;

            });

        },
        async submit(e, index) {
            e.preventDefault();
            this.loader = true;
            const form = new FormData(document.getElementById('form-detalles-' + index));
            let api = unujobs("post", "/detalle", form);
            await api.then(res => {
                let { status, message } = res.data;
                let icono = status ? 'success' : 'error';
                notify({icon: icono, text: message});
            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal :('});
            });
            this.loader = false;
        },
        async saveObservacion() {
            this.loader = true;
            let api = unujobs('post', `/work/${this.param}/observacion`, {
                cronograma_id: this.cronograma.id,
                observacion: this.observacion
            });
            await api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon, text: message});
            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal :('});
            });
            this.loader = false;
        }
    }
}
</script>


<style scoped>
    
    .btn-fixed {
        position: absolute;
        bottom: 20px;
        right: 20px;
    }

</style>