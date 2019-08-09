<template>
    <span>

        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false">
            <template slot="header">
                Calular y Liquidar al trabajador <b class="text-danger">{{ nombre_completo }}</b>
            </template>
            <template slot="content">
                <form class="card-body" id="register-descuento" v-on:submit="submit">

                    <div class="form-group">
                        <label for="">Fecha de Ingreso <small class="text-danger">*</small></label>
                        <input type="date" class="form-control" name="key" v-model="fecha_de_inicio" :disabled="true">
                        <small class="text-danger">{{ errors.key ? errors.key[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Fecha de Cese <small class="text-danger">*</small></label>
                        <input type="date" class="form-control" name="key" v-model="cese">
                        <small class="text-danger">{{ errors.key ? errors.key[0] : '' }}</small>
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
    props: ["redirect", "datos", "theme", "nombre_completo", "fecha_de_inicio"],
    data() {
        return {
            show: false,
            form: {
                key: '',
                descripcion: ''
            },  
            errors: {},
            loader: false,
            edit: false,
            cese: ''
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

