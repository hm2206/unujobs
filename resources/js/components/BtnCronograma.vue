<template>
    <span>

        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false" height="90vh">
            <template slot="header">
                Registro de planilla x mes
            </template>
            <template slot="content">
                <form class="card-body p-relative scroll-y" id="register-cronograma" v-on:submit="submit">
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
                        <label for="">Mes <small class="text-danger">*</small></label>
                        <input type="number" min="1" max="12" name="mes" v-model="form.mes" class="form-control" :disabled="edit">
                        <small class="text-danger">{{ errors.mes ? errors.mes[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Dias <small class="text-danger">*</small></label>
                        <input type="number" name="dias" v-model="form.dias" class="form-control" :disabled="edit">
                        <input type="hidden" name="dias" v-model="form.dias">
                        <small class="text-danger">{{ errors.dias ? errors.dias[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Observación <small class="text-danger">*</small></label>
                        <textarea name="observacion" class="form-control" 
                            v-model="form.observacion"
                        >
                        </textarea>
                        <small class="text-danger">{{ errors.observacion ? errors.observacion[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Adicional</label>
                        <input type="checkbox" name="adicional" v-model="form.adicional" :disabled="edit">
                        <small class="text-danger">{{ errors.adicional ? errors.adicional[0] : '' }}</small>
                    </div>
                </form>
                <div class="card-footer text-right">
                    <button class="btn btn-success" v-if="!loader" v-on:click="submit">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <div class="w-100 text-center" v-if="loader">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </template>
        </modal>
    </span>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ["redirect", "datos", "theme", "mes"],
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
        this.form.mes = this.mes;
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
            const form = new FormData(document.getElementById('register-cronograma'));

            this.loader = true;
            this.errors = {};

            if (this.edit) {
                form.append("_method", "PUT");
                this.request("post", `/cronograma/${this.form.id}`, form);
            }else {
                this.request("post", `/cronograma`, form);
            }
        },
        async request(method, ruta, form) {

            let api = unujobs(method, ruta, form);
            await api.then(async res => {

                console.log(res.data);
                let { status, message } = res.data; 

                if (status) {
                    await notify({icon: 'success', text: message});
                    location.href = this.redirect + `&adicional=${this.form.adicional}`;
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



<style scoped>

    .p-relative {
        position: relative;
    }

    .btn-fixed {
        position: absolute;
        bottom: 20px;
        right: 20px;
    }

    .min-height {
        height: 100%;
    }
</style>
