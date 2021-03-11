<template>
    <span>

        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false">
            <template slot="header">
                Registro de usuario
            </template>
            <template slot="content">
                <form class="card-body scroll-y" id="register-role" v-on:submit="submit">

                    <div class="form-group">
                        <label for="">Clave<small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="key" v-model="form.key">
                        <small class="text-danger">{{ errors.key ? errors.key[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Nombre<small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="name" v-model="form.name">
                        <small class="text-danger">{{ errors.name ? errors.name[0] : '' }}</small>
                    </div>

                    <hr>
                    Modulos

                    <div class="form-group">
                        <hr>
                        <div class="row">
                            <div class="col-md-4" v-for="(modulo, m) in modulos" :key="`modulo-${m}`">
                                <input type="checkbox" name="modulos[]" :value="modulo.id" v-model="tmp_modulos">
                                <span v-text="modulo.name"></span>
                            </div>
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
            modulos: [],
            form: {
                key: "",
                nombre: ""
            },  
            tmp_modulos: [],
            errors: {},
            loader: false,
            edit: false,
        }
    },
    watch: {
        show(nuevo) {
            if(nuevo) {
                this.getModulos();
            }
        }
    },
    mounted() {

        if (this.datos) {
            this.edit = true;
            this.form = this.datos;
        }
    },
    methods: {
        async getModulos() {
            this.loader = true;

            await unujobs("get", "/modulo").then(res => {
                this.modulos = res.data;

                let current = this.form;

                if (current.modulos && current.modulos.length > 0) {
                    current.modulos.filter((e) => {
                        this.tmp_modulos.push(e.id);
                        return e;
                    });
                }

            }).catch(err => console.log(err));

            this.loader = false;
        },
        async submit(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('register-role'));

            this.loader = true;
            this.errors = {};

            if (this.edit) {
                form.append("_method", "PUT");
                this.request("post", `/role/${this.form.id}`, form);
            }else {
                this.request("post", `/role`, form);
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

