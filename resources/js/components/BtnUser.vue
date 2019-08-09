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
                <form class="card-body scroll-y" id="register-user" v-on:submit="submit">

                    <div class="form-group">
                        <label for="">Apellido Paterno<small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="ape_paterno" v-model="form.ape_paterno">
                        <small class="text-danger">{{ errors.ape_paterno ? errors.ape_paterno[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Apellido Materno<small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="ape_materno" v-model="form.ape_materno">
                        <small class="text-danger">{{ errors.ape_materno ? errors.ape_materno[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Nombres<small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="nombres" v-model="form.nombres">
                        <small class="text-danger">{{ errors.nombres ? errors.nombres[0] : '' }}</small>
                    </div>

                    <div class="form-group" v-if="!edit">
                        <label for="">Email<small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="email" v-model="form.email">
                        <small class="text-danger">{{ errors.email ? errors.email[0] : '' }}</small>
                    </div>

                    <div class="form-group" v-if="!edit">
                        <label for="">Contraseña<small class="text-danger">*</small></label>
                        <input type="password" class="form-control" name="password" v-model="form.password">
                        <small class="text-danger">{{ errors.password ? errors.password[0] : '' }}</small>
                    </div>

                    <hr>
                    Roles

                    <div class="form-group">
                        <hr>
                        <div class="row">
                            <div class="col-md-4" v-for="(rol, r) in roles" :key="`role-${r}`">
                                <input type="checkbox" name="roles[]" :value="rol.id" v-model="tmp_roles">
                                <span v-text="rol.name"></span>
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
            roles: [],
            form: {
                ape_paterno: "",
                ape_materno: "",
                nombres: "",
                email: "",
                password: "",
            },  
            tmp_roles: [],
            errors: {},
            loader: false,
            edit: false,
        }
    },
    watch: {
        show(nuevo) {
            if (nuevo) {
                this.getRoles();
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
        async getRoles() {
            this.loader = true;

            await unujobs("get", "/role").then(res => {
                this.roles = res.data;

                let current = this.form;

                if (current.roles && current.roles.length > 0) {
                    current.roles.filter((e) => {
                        this.tmp_roles.push(e.id);
                        return e;
                    });
                }

            }).catch(err => console.log(err));

            this.loader = false;
        },
        async submit(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('register-user'));

            this.loader = true;
            this.errors = {};

            if (this.edit) {
                form.append("_method", "PUT");
                this.request("post", `/user/${this.form.id}`, form);
            }else {
                this.request("post", `/user`, form);
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

