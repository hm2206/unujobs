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

                    <div class="form-group">
                        <label for="">Activo</label>
                        <input type="checkbox" name="activo" v-model="form.activo">
                        <small class="text-danger">{{ errors.descripcion ? errors.descripcion[0] : '' }}</small>
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
                activo: 0
            },  
            config: {
                porcentaje: 0,
                monto: 0,
                minimo: 0
            },
            errors: {},
            loader: false,
            edit: false,
            checked: false
        }
    },
    mounted() {

        if (this.datos) {
            this.edit = true;
            this.form = this.datos;

            if (this.form.config) {
                this.config = this.form.config;
                this.checked = true;
            }
            
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

