<template>
    <div class="card-body">

        <form class="row" id="form-detalle" v-on:submit="saveDetalle">
            <div class="col-md-4">
                <select name="type_educacional_id" class="form-control">
                    <option value="">Seleccionar...</option>
                    <option :value="educacional.id"  v-for="(educacional, educ) in type_educacionales" :key="educ">
                        {{ educacional.descripcion }}
                    </option>
                </select>
                <small class="text-danger">{{ errors.type_educacional_id ? errors.type_educacional_id[0] : '' }}</small>
            </div>
            <div class="col-md-4">
                <input type="number" step="any" name="monto" class="form-control" placeholder="Monto">
                <small class="text-danger">{{ errors.monto ? errors.monto[0] : '' }}</small>
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
                    <div class="col-md-4 mb-1" v-for="(educacional, edu) in educacionales" :key="`educacional-${edu}-${history.id}`">
                        <div class="row align-items-center" v-if="!loader">
                            <div class="col-md-5 text-right">
                                <small class="text-danger">
                                    {{ 
                                        educacional.type_educacional ? 
                                        educacional.type_educacional.key
                                        : '' 
                                    }}
                                </small>
                                    .
                                <small class="text-primary">
                                    <b>{{ 
                                        educacional.type_educacional ? 
                                        educacional.type_educacional.descripcion
                                        : '' 
                                    }}</b>
                                </small>
                            </div>
                            <div class="col-md-5">
                                <input type="number" 
                                    class="form-control" 
                                    :disabled="loader" 
                                    min="0"
                                    v-model="educacional.monto" 
                                >
                            </div>
                        </div>
                    </div>
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
    props: ['cronograma', 'send', 'history'],
    data() {
        return {
            type_educacionales: [],
            educacionales: [],
            tmp_educacional: [],
            loader: false,
            show: true,
            observacion: '',
            errors: {}
        };
    },
    mounted() {
        this.getTypeEducacional();
        this.getDetalles();
    },
    watch: {
        send(nuevo) {
            this.updateTasa();
        }
    },
    methods: {
        getTypeEducacional() {
            let api = unujobs('get', '/type_educacional');
            api.then(res => {
                this.type_educacionales = res.data;
            }).catch(err => {

            });
        },
        async saveDetalle(e) {
            e.preventDefault();
            this.loader = true;
            const form = new FormData(document.getElementById('form-detalle'));
            form.append('historial_id', this.history.id);
            let api = unujobs('post', '/educacional', form);
            await api.then(res => {
                let { status, message, body } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});
                this.getDetalles();
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
            let  api = unujobs('get', `/historial/${this.history.id}/educacional`);
            this.tmp_educacional = [];
            api.then(res => {
                this.educacionales = res.data;
                this.educacionales.filter(t => {
                    this.tmp_educacional.push(t.monto);
                });
            }).catch(err => {

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
        async updateTasa() {
            this.loader = true;
            const form = new FormData;
            let educacionales = [];
            // preparamos los datos para enviar
            await this.educacionales.filter((obj, i) => {
                educacionales.push({
                    id: obj.id,
                    monto: obj.monto
                });
            });
            // agregamos al formdata
            form.append('educacionales', JSON.stringify(educacionales));
            form.append('_method', 'PUT');
            let api = unujobs('post', `/educacional/${this.history.id}/historial`, form);
            await api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({ icon, text: message });
            }).catch(err => {
                notify({ icon: 'error', text: 'algo salió mal :C' });
            });
            // actualizamos el boton
            this.$emit('ready');
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