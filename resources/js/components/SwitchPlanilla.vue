<template>
    <div>

        <modal @close="show = false" col="col-md-5" :show="show" height="35vh" style="padding-top: 10px;">
            <template slot="header">
                Confirmar el cambio del estado de la planilla
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                    <div class="group">
                        <input type="password" 
                            placeholder="Ingrese su contraseña para confirmar" 
                            name="password" 
                            class="form-control"
                            v-model="password"
                            :disabled="loader"
                        >
                    </div>
                </div>
                <div class="card-footer">
                    <button :class="`btn btn-${estado ? 'danger' : 'primary'}`" 
                        v-if="!loader"
                        v-on:click="send"
                    >
                        <i :class="`fas fa-${estado ? 'times' : 'check'}`"></i> {{ estado ? 'Desactivar' : 'Activar' }} Planilla
                    </button>

                    <div class="text-center w-100" v-if="loader">
                        <span class="spinner-border text-primary"></span>
                    </div>
                </div>
            </template>
        </modal>

        <button :class="`btn btn-circle btn-lg btn-${estado ? 'primary' : 'danger' } p-fixed`"
            :title="estado ? 'Desactivar' : 'Activar'"
            v-on:click="show = true"
        >
            <i :class="`fas fa-${estado  ? 'check' : 'times'}`"></i>
        </button>

    </div>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: {
        active: {
            type: Number,
            default: 0
        },
        param: {
            type: String
        },
        user_id: {
            type: String
        }
    },
    data() {
        return {
            show: false,
            password: '',
            estado: false,
            loader: false
        }
    },
    mounted() {
        this.estado = this.active ? true : false;
    },
    methods: {
        async send(e) {
            let api = unujobs("post", `/cronograma/${this.param}/estado`, {
                user: this.user_id,
                password: this.password
            });

            this.loader = true;

            await api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'danger';
                this.estado = !this.estado;
                console.log(this.estado);
                this.password = "";
                notify({icon, text: message});
            }).catch(err => {
                notify({icon: 'error', text: 'Las credenciales son incorrectas'});
            });

            this.loader = false;    
        }
    }
}
</script>

<style>

    .p-fixed {
        position: fixed;
        bottom: 25px;
        right: 25px;
    }

</style>