<template>
    <span>

        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false">
            <template slot="header">
                Registro de descuento
            </template>
            <template slot="content">
                <form class="card-body scroll-y" id="register-descuento" v-on:submit="submit">

                    <div class="form-group">
                        <label for="">Clave <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="key" v-model="form.key" :disabled="edit">
                        <small class="text-danger">{{ errors.key ? errors.key[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Descripción <small class="text-danger">*</small></label>
                        <textarea name="descripcion" class="form-control" v-model="form.descripcion"></textarea>
                        <small class="text-danger">{{ errors.descripcion ? errors.descripcion[0] : '' }}</small>
                    </div>

                    <hr>

                    <div class="form-group" v-if="edit">
                        <label for="">Descuento SNP</label>
                        <input type="checkbox" name="obligatorio" v-model="form.obligatorio" value="1">
                    </div>

                    <div class="form-group" v-if="form.obligatorio">
                        <div class="row align-items-center">
                            <label for="" class="col-md-3">Porcentaje%</label>
                            <input type="number" class="form-control col-md-3" name="snp_porcentaje" v-model="form.snp_porcentaje">
                        </div>
                        <hr>
                    </div>

                    <div class="form-group" v-if="edit">
                        <label for="">Descuento de Essalud</label>
                         <input type="checkbox" name="essalud" v-model="form.essalud" value="1">
                    </div>

                    <div class="form-group" v-if="form.essalud">
                        <div class="row align-items-center">
                            <label for="" class="col-md-3">Porcentaje%</label>
                            <input type="number" class="form-control col-md-3" name="essalud_porcentaje" v-model="form.essalud_porcentaje">
                        </div>

                        <div class="row align-items-center mt-1">
                            <label for="" class="col-md-3" title="Solo aplica cuando la base imponible es menor a RMV">RMV</label>
                            <input type="number" class="form-control col-md-3" name="minimo" v-model="form.minimo">
                        </div>

                        <div class="row align-items-center mt-1">
                            <label for="" class="col-md-3" title="Solo aplica cuando la base imponible es menor a RMV">Defecto</label>
                            <input type="number" class="form-control col-md-3" name="monto" v-model="form.monto">
                        </div>
                    </div>

                </form>
                <div class="card-footer text-right">
                    <button class="btn btn-success" :disabled="loader" v-on:click="submit">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </template>
        </modal>
    </span>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ["redirect", "datos", "theme"],
    data() {
        return {
            show: false,
            form: {
                key: '',
                descripcion: '',
                obligatorio: 0,
                snp_porcentaje: 0,
                essalud: 0,
                essalud_porcentaje: 0,
                monto: 83.70,
                minimo: 0
            },  
            errors: {},
            loader: false,
            edit: false,
        }
    },
    mounted() {

        if (this.datos) {
            this.edit = true;
            this.form = this.datos;
        }
    },
    methods: {
        async submit(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('register-descuento'));

            this.loader = true;
            this.errors = {};

            if (this.edit) {
                form.append("_method", "PUT");
                this.request("post", `/descuento/${this.form.id}`, form);
            }else {
                this.request("post", `/descuento`, form);
            }
        },
        async request(method, ruta, form) {

            let api = unujobs(method, ruta, form);
            await api.then(async res => {

                let { status, message } = res.data; 
                console.log(res);

                if (status) {
                    await notify({icon: 'success', text: message});
                    location.href = this.redirect;
                }else {
                    await notify({icon: 'error', text: message});
                }

            }).catch(err => {
                this.errors = err.response.data.errors;
            });

            this.loader = false;
        }
    }
}
</script>

