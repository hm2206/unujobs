<template>
    <span>

        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false">
            <template slot="header">
                Registro de cargo
            </template>
            <template slot="content">
                <form class="card-body" id="register-cargo" v-on:submit="submit">
                    <div class="form-group">
                        <label for="">Planilla <small class="text-danger">*</small></label>
                        <select name="planilla_id" class="form-control" 
                            v-model="form.planilla_id"
                            :disabled="edit"
                        >
                            <option value="">Seleccionar...</option>
                            <option :value="planilla.id" v-for="(planilla, p) in planillas" :key="`planilla-${p}`">
                                {{ planilla.descripcion }}
                            </option>
                        </select>
                        <input type="hidden" name="planilla_id" v-model="form.planilla_id">
                        <small class="text-danger">{{ errors.planilla_id ? errors.planilla_id[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Descripcion <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="descripcion" v-model="form.descripcion">
                        <small class="text-danger">{{ errors.descripcion ? errors.descripcion[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">P.A.P <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" name="tag" v-model="form.tag">
                        <small class="text-danger">{{ errors.tag ? errors.tag[0] : '' }}</small>
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
            planillas: [],
            form: {
                planilla_id: "",
                observacion: "",
                dias: 30,
                adicional: 0,
            },  
            errors: {},
            loader: false,
            edit: false,
        }
    },
    mounted() {
        this.getPlanillas();

        if (this.datos) {
            this.edit = true;
            this.form = this.datos;
        }
    },
    methods: {
        async getPlanillas() {
            this.loader = true;

            await unujobs("get", "/planilla").then(res => {
                this.planillas = res.data;
            }).catch(err => console.log(err));

            this.loader = false;
        },
        async submit(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('register-cargo'));

            this.loader = true;
            this.errors = {};

            if (this.edit) {
                form.append("_method", "PUT");
                this.request("post", `/cargo/${this.form.id}`, form);
            }else {
                this.request("post", `/cargo`, form);
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

