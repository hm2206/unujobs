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
                    Modulos

                     <div id="accordianId" role="tablist" aria-multiselectable="true">

                        <div class="card mb-3" v-for="(modulo, mo) in modulos" :key="`modulos-${mo}`">
                            <div class="card-header" role="tab" :id="`section${mo}HeaderId`">
                                <h6 class="mb-0">
                                    <a  aria-expanded="true" :aria-controls="`section${mo}ContentId`"
                                        data-toggle="collapse" data-parent="#accordianId" :href="`#section${mo}ContentId`" 
                                    >
                                        {{ modulo.name }}
                                        <input type="hidden" :value="modulo.id" name="modulos[]" v-if="counts[mo] > 0" checked/>
                                    </a>
                                </h6>
                            </div>
                            <div :id="`section${mo}ContentId`" class="collapse in " role="tabpanel" :aria-labelledby="`section${mo}HeaderId`">
                                <div class="card-body">
                                    <ol>    
                                        <li v-for="(sub, s) in modulo.modulos" :key="`sub-${s}`">
                                            <input type="checkbox" name="modulos[]" :value="sub.id" v-model="tmp_modulos"
                                                v-on:change="cheking($event, mo)"
                                            > {{ sub.name }}
                                        </li>
                                    </ol>
                                </div>
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
                ape_paterno: "",
                ape_materno: "",
                nombres: "",
                email: "",
                password: "",
            },  
            tmp_modulos: [],
            errors: {},
            loader: false,
            edit: false,
            counts: []
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
        cheking(e, index) {
            let { checked } = e.target;

            this.counts[index] = checked ? this.counts[index] + 1 : this.counts[index] - 1;

        },
        async getModulos() {
            this.loader = true;

            await unujobs("get", "/modulo").then(res => {
                this.modulos = res.data;

                this.modulos.filter(e => this.counts.push(0))

                let m_parent = this.form.modulos;

                if (m_parent.length > 0) {

                    let iter = 0;
                    
                    for (let parent of m_parent) {

                        this.tmp_modulos.push(parent.id);
                        this.counts[iter] = 1;

                        if (typeof m_parent.modulos == 'array') {
                            
                            
                            for (let son of m_parent.modulos) {
                            
                                this.tmp_modulos.push(son.id);

                            }

                        }

                        iter++;

                    }

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

