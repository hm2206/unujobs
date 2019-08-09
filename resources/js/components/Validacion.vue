<template>
    <span>
        <button :class="`btn ${theme ? theme : 'btn-sm btn-success'}`" v-on:click="showVentana">
            <i class="fas fa-file-excel"></i>
            <span v-text="btn_text"></span>
        </button>

        <div class="ventana" v-show="ventana">
            <div class="row h-100 justify-content-center mt-5">
                <div class="col-md-5">
                    <form class="card" :method="method" :action="url" v-on:submit="verify" :id="id" enctype="multipart/form-data">
                        <div class="card-header">
                            Dialogo de Confirmación
                        </div>
                        <div class="card-body">

                            <input type="hidden" name="_token" :value="token">
                            <slot></slot>

                            <div class="form-group">
                                <label for="" class="form-control-label">
                                    Contraseña de Confirmación
                                </label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <button class="btn btn-primary">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger" v-on:click="hideVentana">
                                <i class="fas fa-ban"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>


<style scoped>
    .ventana {
        width: 100%;
        height: 100%;
        position: fixed;
        background: rgba(0, 0, 0, 0.5);
        top: 0px;
        left: 0px;
        z-index: 100;

    }
</style>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['btn_text', 'method', 'url', 'sync', 'id', 'token', 'theme'],
    data() {
        return {
            ventana: false
        }
    },
    methods: {
        showVentana(e) {
            e.preventDefault();
            this.ventana = true;
        },
        hideVentana(e) {
            e.preventDefault();
            this.ventana = false
        },
        verify(e) {

            if (this.sync) {
                e.preventDefault();

                const form = new FormData(document.getElementById(this.id));

                let api = unujobs(this.method, this.url, form);
                api.then(res => {
                    console.log(res);
                    notify({icon: 'success', text: res.data});
                }).catch(err => {
                    console.log(err);
                });

            }
    
        }
    }
}
</script>
