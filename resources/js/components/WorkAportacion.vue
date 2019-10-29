<template>
    <div class="card-body">
        <div class="row">
            <form class="col-md-12" id="add-aportacion" v-on:submit="add">
                <div class="row">
                    <div class="col-md-5">
                        <select name="type_aportacion_id" class="form-control">
                            <option value="">Seleccionar</option>
                            <option
                                v-for="type in type_aportaciones"
                                :key="type.id"
                                :value="type.id"
                            >{{ type.descripcion }}</option>
                        </select>
                        <input type="hidden" name="historial_id" :value="history.id">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Agregar aportación
                        </button>
                    </div>
                </div>
                <hr>
            </form>

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3"
                        v-for="aportacion in aportaciones"
                        :key="`aporte-${aportacion.id}`"
                    >
                        <span class="btn btn-dark">{{ aportacion.type_aportacion 
                            ? aportacion.type_aportacion.descripcion : '' }}
                            <i class="fas fa-arrow-right text-warning"></i>
                            <b class="btn btn-sm btn-warning text-dark">
                               <b>S/. {{ aportacion.monto }}</b>
                            </b>
                            <span class="btn btn-circle btn-sm btn-danger"
                                style="cursor: pointer;"
                                v-on:click="leave(aportacion.id)"
                            >
                                <i class="fas fa-trash"></i>
                            </span>
                        </span>
                    </div>
                </div>
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
            type_aportaciones: [],
            aportaciones: [],
            loader: false,
            tmp_descuentos: [],
            total: 0,
            base: 0,
            tota_neto: 0
        };
    },
    mounted() {
        this.getTypeAportaciones();
        this.getAportaciones();
    },
    watch: {
        history(nuevo) {
            if (nuevo) {
                this.getAportaciones();
            }
        }
    },
    methods: {
        getTypeAportaciones() {
            let api = unujobs('get', '/type_aportacion');
            api.then(res => {
                this.type_aportaciones = res.data;
            }).catch(err => {

            });
        },
        getAportaciones() {
            let api = unujobs('get', `/historial/${this.history.id}/aportacion`);
            api.then(res => {
                this.aportaciones = res.data;
            }).catch(err => {

            });
        },
        add(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('add-aportacion'))
            let api = unujobs('post', '/aportacion', form);
            api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({ icon, text: message });
                this.getAportaciones();
            }).catch(err => {
                notify({ icon: 'error', text: 'Algo salió mal :(' });
            });
        },
        async leave(e) {
            let answer = await confirm('Desea quitar la aportación del empleador');
            if (answer) {
                let api = unujobs('post', `/aportacion/${e}`, { _method: 'DELETE' });
                api.then(res => {
                    let  { status, message } = res.data;
                    let icon = status ? 'success' : 'error';
                    notify({ icon, text: message });
                    this.getAportaciones();
                }).catch(err => {
                    notify({ icon: 'error', text: 'Algo salió mal :(' });
                });
            }
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

    .form-control {
        font-size: 14px !important;
        font-weight: bold;
    }

</style>