<template>
    <span>
        <button :class="`btn ${theme}`" v-on:click="show = true" :disabled="loader">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false" height="50vh">
            <template slot="header">
                Report de alta y bajas
            </template>
            <template slot="content">
                <div class="card-body scroll-y">
                    <div class="row">

                        <div class="col-md-6" v-if="!only">
                            <div class="from-group" >
                                <label for="">Mes</label>
                                <input type="number" class="form-control" v-model="newMes" max="12">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="from-group">
                                <label for="">Año</label>
                                <input type="number" class="form-control" v-model="year">
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="from-group">
                                <input type="checkbox" v-model="only">
                                <label for="">Solo año</label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer text-right">
                    <div class="from-group">
                        <button class="btn btn-success" v-on:click="send">
                            <div class="fas fa-save"></div> Guardar
                        </button>
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
    props: ['theme', "id", "mes", "year"],
    data() {
        return {
            loader: false,
            show: false,
            only: false,
            newMes: 0
        }
    },
    mounted(){
        this.newMes = this.mes
    },
    watch: {
        only(nuevo) {

            if (nuevo) {
                this.newMes = 0;
            }else {
                this.newMes = this.mes;
            }

        }
    },
    methods: {
        send() {

            let api = unujobs("post", `/export/alta-baja/${this.year}/${this.newMes}`);

            api.then(res => {
                
                let { status, message } = res.data;

                let icon = status ? "success" : "error";

                notify({icon, text: message});

                console.log(res);

            }).catch(err => {
                
                notify({icon: 'error', text: "Algo salió mal"});
                
            });

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

