<template>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-1" v-for="(remuneracion, re) in remuneraciones" :key="`remuneracion-${re}-${param}`">
                <form class="row align-items-center" id="update-remuneracion" v-on:submit="submit">
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
                                remuneracion.type_remuneracion.descripcion
                                : '' 
                            }}</b>
                        </small>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control" :name="remuneracion.id" 
                            :disabled="loader" 
                            min="0"
                            v-model="tmp_remuneraciones[re]" 
                        >
                    </div>
                </form>
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
    props: ['categoria', 'mes', 'year', 'adicional', 'numero', 'param', 'send', 'info'],
    data() {
        return {
            remuneraciones: [],
            loader: false,
            cronograma: {},
            total: 0,
            tmp_remuneraciones: []
        };
    },
    mounted() {
        this.getRemuneraciones();
    },
    watch: {
        send(nuevo) {
            if (nuevo) {
                this.submit();
            }
        }
    },
    methods: {
        async getRemuneraciones() {

            let adicional = this.adicional ? 1 : 0;

            let  api = unujobs(
                'get',
                `/work/${this.param}/remuneracion?mes=${this.mes}&year=${this.year}&adicional=${adicional}&categoria_id=${this.categoria}&numero=${this.numero}`
            );

            await api.then(res => {

                let { remuneraciones, cronograma, total, numeros } = res.data;
                this.remuneraciones = remuneraciones;
                this.cronograma = cronograma;
                this.$emit('get-numeros', numeros);
                this.total = total;
                this.remuneraciones.filter(re => {
                    this.tmp_remuneraciones.push(re.monto);
                });

            }).catch(err => {
                this.cronograma = {};
            });

            this.$emit('get-cronograma', this.cronograma);

        },
        async submit(e) {

            if (e) {
                e.preventDefault();
            }

            const form = new FormData;
            this.loader = true;
            
            this.remuneraciones.filter((re, iter) => {
                form.append(re.id, this.tmp_remuneraciones[iter]);
            });

            form.append("_method", "PUT");
            form.append("cronograma_id", this.cronograma.id);
            form.append("cargo_id", this.info.cargo_id);
            form.append("categoria_id", this.info.categoria_id);
            form.append("planilla_id", this.info.planilla_id);

            let api = unujobs("post", `/work/${this.param}/remuneracion`, form);
            await api.then(res => {
                let { status, message, body } = res.data;
                let icon = status ? 'success' : 'error';
                this.total = body ? body : this.total;
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

</style>