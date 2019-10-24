<template>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-1" v-for="(remuneracion, re) in remuneraciones" :key="`remuneracion-${re}-${history.id}`">
                <form class="row align-items-center" id="update-remuneracion" v-on:submit="submit" v-if="!loader">
                    <div class="col-md-7">
                        <small class="text-danger">
                            {{ 
                                remuneracion.type_remuneracion ? 
                                remuneracion.type_remuneracion.key
                                : '' 
                            }}
                        </small>
                            .
                        <small class="text-primary">
                            <b>{{ 
                                remuneracion.type_remuneracion ? 
                                remuneracion.type_remuneracion.alias
                                : '' 
                            }}</b>
                        </small>
                    </div>
                    <div class="col-md-5">
                        <input type="number" 
                            class="form-control" 
                            :disabled="loader || !remuneracion.edit" 
                            min="0"
                            v-model="tmp_remuneraciones[re]" 
                        >
                    </div>
                </form>
            </div>

            <div class="text center col-md-12" v-if="loader">
                <div class="spinner-border text-warning"></div>
            </div>

            <div class="w-100 mt-4 text-center" v-if="!loader && remuneraciones.length == 0">
                    No hay registros disponibles!
            </div>

            <div class="w-100 mt-4 text-center" v-if="!loader && remuneraciones.length > 0">
                    <b>Total: S./ {{ total }}</b>
            </div>

        </div>
    
    </div>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['cronograma', 'send', 'history'],
    data() {
        return {
            remuneraciones: [],
            loader: false,
            total: 0,
            tmp_remuneraciones: []
        };
    },
    mounted() {
        this.getRemuneraciones();   
    },
    watch: {
        async history() {
            await this.getRemuneraciones();
        },
        send(nuevo) {
            if (nuevo) {
                this.submit();
            }
        },
    },
    methods: {
        async getRemuneraciones() {

            let  api = unujobs('get', `/historial/${this.history.id}/remuneracion`);
            this.tmp_remuneraciones = [];
            await api.then(async res => {
                let { remuneraciones, total_bruto } = res.data;
                this.total = total_bruto;
                this.remuneraciones = await remuneraciones.filter(re => {
                    this.tmp_remuneraciones.push(re.monto);
                    return re;
                });
            }).catch(err => {
                console.log('algo salio mal al obtener las remuneraciones');
            });

        },
        async submit(e) {

            if (e) {
                e.preventDefault();
            }

            const form = new FormData;
            this.loader = true;
            let payload = [];
            
            this.remuneraciones.filter((re, iter) => {
                let monto = this.tmp_remuneraciones[iter];
                payload.push({
                    id: re.id,
                    monto
                });
            });

            form.append("_method", "PUT");
            form.append("remuneraciones", JSON.stringify(payload));

            let api = unujobs("post", `/type_remuneracion/${this.history.id}/all`, form);
            await api.then(res => {
                let { status, message, total_bruto } = res.data;
                let icon = status ? 'success' : 'error';
                this.total = total_bruto;
                notify({icon: icon, text: message});
            }).catch(err => {
                notify({icon: 'danger', text: err.message});
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