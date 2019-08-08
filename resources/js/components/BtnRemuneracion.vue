<template>
    <span>

        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false" heigth="300px">
            <template slot="header">
                Registro de remuneración
            </template>
            <template slot="content">
                <form class="card-body" id="register-remuneracion" v-on:submit="submit">

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
                        <label for="">No considerar a la base imponible  <small class="text-dark">(Opcional)</small></label>
                        <br>
                        <input type="checkbox" name="base" v-model="form.base">
                        <small class="text-danger">{{ errors.base ? errors.base[0] : '' }}</small>
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
                base: ''
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
            const form = new FormData(document.getElementById('register-remuneracion'));

            this.loader = true;
            this.errors = {};

            if (this.edit) {
                form.append("_method", "PUT");
                this.request("post", `/remuneracion/${this.form.id}`, form);
            }else {
                this.request("post", `/remuneracion`, form);
            }
        },
        async request(method, ruta, form) {

            let api = unujobs(method, ruta, form);
            await api.then(async res => {

                let { status, message } = res.data; 

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

