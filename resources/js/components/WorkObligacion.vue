<template>
    <div class="card-body">

        <form class="row align-items-center" id="register-obligacion" v-on:submit="register"
            v-if="show"
        >
            <div class="col-md-3">
                <input type="text" class="form-control" v-model="form.beneficiario" name="beneficiario" placeholder="Beneficiario">
                <small class="text-danger">
                    {{ errors.beneficiario ? errors.beneficiario[0] : '' }}
                </small>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="numero_de_documento" placeholder="DNI"
                    v-model="form.mumero_de_documento"
                >
                <small class="text-danger">
                    {{ errors.numero_de_documento ? errors.numero_de_documento[0] : '' }}
                </small>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="numero_de_cuenta" placeholder="N° de Cuenta"
                    v-model="form.numero_de_cuenta"
                >
                <small class="text-danger">
                    {{ errors.numero_de_cuenta ? errors.numero_de_cuenta[0] : '' }}
                </small>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="monto" placeholder="Monto" step="any"
                    v-model="form.monto"
                >
                <small class="text-danger">
                    {{ errors.monto ? errors.monto[0] : '' }}
                </small>
                <input type="hidden" name="work_id" :value="param">
                <input type="hidden" name="cronograma_id" :value="cronograma.id">
                <input type="hidden" name="info_id" :value="info.id">
            </div>
            <div class="col-md-1">
                <button class="btn btn-sm btn-success" v-if="!loader">
                    <i class="fas fa-save"></i>
                </button>
                <div class="spinner-border text-primary" v-if="loader"></div>
            </div>

        </form>

        <hr v-if="show">

        <form class="row align-items-center mt-3" 
            v-on:submit="submit($event, obligacion.id)" v-for="(obligacion, obl) in obligaciones" 
            :key="`obligacion-${obl}`"
            id="update-obligacion"
        >
            <div class="col-md-3">
                <input type="text" class="form-control" name="up_beneficiario" placeholder="Beneficiario" 
                    :value="obligacion.beneficiario"
                    :disabled="edit"
                >
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="up_numero_de_documento" placeholder="DNI" 
                    :value="obligacion.numero_de_documento"
                    :disabled="edit"
                >
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="up_numero_de_cuenta" placeholder="N° de Cuenta" 
                    :value="obligacion.numero_de_cuenta"
                    :disabled="edit"
                >
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="up_monto" step="any" placeholder="Monto" 
                    v-model="obligacion.monto"
                    :disabled="edit"
                >
                <input type="hidden" name="up_work_id" :value="param">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-success"
                    :disabled="edit"
                >
                    <i class="fas fa-save"></i>
                </button>
                <button class="btn btn-sm btn-danger"
                    :disabled="edit"
                    v-on:click="destroy($event, obligacion.id, obl)"
                >
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </form>

        <div class="mt-3 w-100 text-center" v-if="obligaciones.length == 0">
            No hay registros disponibles!
        </div>
    
    </div>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['categoria', 'mes', 'year', 'tmp_cronograma', 'adicional', 'numero', 'param', 'send', 'info'],
    data() {
        return {
            obligaciones: [],
            loader: false,
            show: true,
            errors: {},
            cronograma: {},
            form: {
                beneficiario: '',
                numero_de_documento: '',
                numero_de_cuenta: '',
                monto: ''
            },
            edit: false
        };
    },
    watch: {
        send(nuevo) {
            if (nuevo) {
                this.submit();
            }
        }
    },
    mounted() {
        this.cronograma = this.tmp_cronograma;
        this.getObligaciones();
    },
    methods: {
        getObligaciones() {

            let adicional = this.adicional ? 1 : 0;

            let  api = unujobs(
                'get',
                `/info/${this.param}/obligacion?cronograma_id=${this.cronograma.id}`
            );

            api.then(res => {

                this.obligaciones = res.data;
                this.show = true;

            }).catch(err => {

                console.log("algo salió mal");
                this.show = false;

            });

        },
        async register(e) {
            e.preventDefault();
            this.loader = true;

            const form = new FormData(document.getElementById('register-obligacion'));
            let api = unujobs("post", "/obligacion", form);
            await api.then(res => {
                let { status, message, body } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});

                if (status) {
                    this.obligaciones.push(body);
                    this.form = {};
                }

            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal'});
                this.errors = err.response.data.errors;
            });

            this.loader = false;
        },
        async submit(e, id) {

            if (e) {
                e.preventDefault();
            }

            const form = new FormData(document.getElementById('update-obligacion'));
            this.edit = true;
            form.append("_method", "PUT");

            let api = unujobs("post", `/obligacion/${id}`, form);
            await api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});
            }).catch(err => {
                notify({icon: 'danger', text: err.message});
            });

            this.edit = false;
            this.$emit("ready");
        },
        destroy(e, id, index) {

            e.preventDefault();
            let api = unujobs("post", `/obligacion/${id}`,{
                _method: 'DELETE'
            });
            api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});
                this.obligaciones.splice(index, 1);
            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal'});
            });

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

</style>