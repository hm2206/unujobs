<template>
    <span>

        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false">
            <template slot="header">
                Registro de Modulo
            </template>
            <template slot="content">
                <form class="card-body scroll-y" id="register-modulo" v-on:submit="submit">
                    <div class="form-group">
                        <label for="">Modulos</label>
                        <select name="modulo_id" class="form-control" 
                            v-model="form.modulo_id"
                            :disabled="edit"
                        >
                            <option value="">Seleccionar...</option>
                            <option :value="modulo.id" v-for="(modulo, m) in modulos" :key="`planilla-${m}`">
                                {{ modulo.name }}
                            </option>
                        </select>
                        <small class="text-danger">{{ errors.modulo_id ? errors.modulo_id[0] : '' }}</small>
                        <input type="hidden" name="modulo_id" v-model="form.modulo_id">
                    </div>

                    <div class="form-group">
                        <label for="">Nombre <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="name" v-model="form.name">
                        <small class="text-danger">{{ errors.name ? errors.name[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Ruta</label>
                        <input type="text" class="form-control" name="ruta" v-model="form.ruta">
                        <small class="text-danger">{{ errors.ruta ? errors.ruta[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Icono</label>
                        <input type="text" class="form-control" name="icono" v-model="form.icono">
                        <small class="text-danger">{{ errors.icono ? errors.icono[0] : '' }}</small>
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
                modulo_id: "",
                name: "",
                ruta: "",
                icono: "",
            },  
            errors: {},
            loader: false,
            edit: false,
        }
    },
    watch: {
        show(nuevo) {
            if (nuevo) {
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
            }).catch(err => console.log(err));

            this.loader = false;
        },
        async submit(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('register-modulo'));

            this.loader = true;
            this.errors = {};

            if (this.edit) {
                form.append("_method", "PUT");
                this.request("post", `/modulo/${this.form.id}`, form);
            }else {
                this.request("post", `/modulo`, form);
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

