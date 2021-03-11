<template>
    <span>
        <button :class="`btn ${theme}`" v-on:click="show = true" :disabled="loader">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false" height="50vh">
            <template slot="header">
                Reporte general del Personal
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
                                <input type="number" class="form-control" v-model="newYear">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <div class="from-group">
                        <a :href="report" class="btn btn-danger" v-if="report">
                            <i class="fas fa-file-pdf"></i> Ver Reporte
                        </a>

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
    props: ['theme', "id", "mes", "year", 'report'],
    data() {
        return {
            loader: false,
            show: false,
            only: false,
            newMes: 0,
            newYear: 2019,
        }
    },
    mounted(){
        this.newMes = this.mes;
        this.newYear = this.year;
    },
    watch: {
        only(nuevo) {

            if (nuevo) {
                this.newMes = 0;
                this.newYear = 2019;
            }else {
                this.newMes = this.mes;
                this.newYear = this.year;
            }

        }
    },
    methods: {
        send() {

            let api = unujobs("post", `/report-personal`, {
                year: this.year,
                mes: this.newMes,
            });

            api.then(res => {
                
                let { status, message } = res.data;

                let icon = status ? "success" : "error";

                notify({icon, text: message}).then(res => {
                    this.show = false;
                });

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

