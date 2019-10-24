<template>
    <form class="card-body" :id="`work-update-${history.id}`">
        <div class="row">

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Apellido Paterno</b></small>
                    <input type="text" name="ape_paterno" :disabled="loader" class="form-control uppercase" v-model="work.ape_paterno">
                    <small class="text-danger">{{ errors.ape_paterno ? errors.ape_paterno[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Apellido Materno</b></small>
                    <input type="text" :disabled="loader" class="form-control uppercase" v-model="work.ape_materno" name="ape_materno">
                    <small class="text-danger">{{ errors.ape_materno ? errors.ape_materno[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Nombres</b></small>
                    <input type="text" :disabled="loader" class="form-control uppercase" v-model="work.nombres" name="nombres">
                    <small class="text-danger">{{ errors.nombres ? errors.nombres[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>N° Documento</b></small>
                    <input type="text" :disabled="loader" class="form-control" v-model="work.numero_de_documento" name="numero_de_documento">
                    <small class="text-danger">{{ errors.numero_de_documento ? errors.numero_de_documento[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Fecha de Nacimiento</b></small>
                    <input type="date" :disabled="loader" class="form-control" v-model="work.fecha_de_nacimiento" name="fecha_de_nacimiento">
                    <small class="text-danger">{{ errors.fecha_de_nacimiento ? errors.fecha_de_nacimiento[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Email</b></small>
                    <input type="email" :disabled="loader" class="form-control" v-model="work.email" name="email">
                    <small class="text-danger">{{ errors.email ? errors.email[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Dirección</b></small>
                    <input type="text" :disabled="loader" class="form-control" v-model="work.direccion" name="direccion">
                    <small class="text-danger">{{ errors.direccion ? errors.direccion[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Profesión</b></small>
                    <input type="text" :disabled="loader" class="form-control" v-model="work.profesion" name="profesion">
                    <small class="text-danger">{{ errors.profesion ? errors.profesion[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>N° Teléfono</b></small>
                    <input type="tel" :disabled="loader" class="form-control" v-model="work.phone" name="phone">
                    <small class="text-danger">{{ errors.phone ? errors.phone[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Sexo</b></small>
                    <select name="sexo" :disabled="loader" v-model="work.sexo" class="form-control">
                        <option value="1">Masculino</option>
                        <option value="0">Femenino</option>
                    </select>
                    <small class="text-danger">{{ errors.sexo ? errors.sexo[0] : '' }}</small>
                </div>
            </div>

        </div>
    </form>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['history', 'send'],
    data() {
        return {
            errors: {},
            work: {},
            loader : false
        };
    },
    mounted() {
        this.work = this.history.work;
    },
    watch: {
        async history() {
            this.work = await this.history.work;
        },
        send(nuevo) {
            if (nuevo) {
                this.submit();
            }
        }
    },
    methods: {
        async submit(e) {
            if (e) {
                e.preventDefault();
            }
            this.loader = true;
            const form = new FormData(document.getElementById(`work-update-${this.history.id}`));
            form.append('_method', "PUT");
            let api = unujobs("post", `/work/${this.work.id}`, form);
            await api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon, text: message});
            }).catch(err => {
                let { data } = res.response;

                if (Object.keys(data.errors).length > 0) {
                    this.errors = data.errors;
                }else {
                    notify({icon: 'error', text: 'Algo salió mal'});
                }

            });
            this.loader = false;
            this.$emit("ready");
        }
    }
}
</script>


<style scoped>
    
    .btn-fixed {
        position: absolute;
        bottom: 20px;
        right: 20px;
    }

    .form-control {
        font-size: 14px !important;
        font-weight: bold;
    }

</style>