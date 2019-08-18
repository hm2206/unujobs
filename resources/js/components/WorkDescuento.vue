<template>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-1" v-for="(descuento, de) in descuentos" :key="`descuento-${de}`">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <small class="text-danger">
                            {{ 
                                descuento.type_descuento ? 
                                descuento.type_descuento.key
                                : '' 
                            }}
                        </small>
                            .
                        <small class="text-primary">
                            <b>{{ 
                                descuento.type_descuento ? 
                                descuento.type_descuento.descripcion
                                : '' 
                            }}</b>
                        </small>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control" :name="descuento.id" 
                        :disabled="loader || !descuento.edit" v-model="tmp_descuentos[de]" min="0">
                    </div>
                </div>
            </div>

            <div class="w-100 mt-4 text-center" v-if="!loader && descuentos.length == 0">
                    No hay registros disponibles!
            </div>

            <div class="w-100 mt-4 text-center" v-if="!loader && descuentos.length > 0">
                    <b class="text-dark">Total Descuentos: S./ {{ total }}</b> <br>
                    <b class="text-dark">Base Imponible: S./ {{ base }}</b> <br>
                    <b class="text-dark">Total Neto: S./ {{ total_neto }}</b> <br>
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
            descuentos: [],
            loader: false,
            cronograma: {},
            tmp_descuentos: [],
            total: 0,
            base: 0,
            tota_neto: 0
        };
    },
    mounted() {
        this.getDescuentos();
    },
    watch: {
        send(nuevo) {
            if (nuevo) {
                this.submit();
            }
        }
    },
    methods: {
        getDescuentos() {

            let adicional = this.adicional ? 1 : 0;

            let  api = unujobs(
                'get',
                `/work/${this.param}/descuento?mes=${this.mes}&year=${this.year}&adicional=${adicional}&categoria_id=${this.categoria}&numero=${this.numero}`
            );

            api.then(res => {

                let { descuentos, cronograma, base, total, total_neto } = res.data;
                this.descuentos = descuentos;
                this.cronograma = cronograma;
                this.base = base;
                this.total = total;
                this.total_neto = total_neto;
                this.descuentos.filter(des => {
                    this.tmp_descuentos.push(des.monto);
                });

                this.$emit('get-cronograma', cronograma);

            }).catch(err => {
                console.log("algo salió mal");
            });

        },
        async submit(e) {

            if (e) {
                e.preventDefault();
            }

            const form = new FormData;
            this.descuentos.filter((des, iter) => {
                form.append(des.id, this.tmp_descuentos[iter]);
            });
            this.loader = true;
            form.append("_method", "PUT");
            form.append("cronograma_id", this.cronograma.id);
            form.append("cargo_id", this.info.cargo_id);
            form.append("categoria_id", this.info.categoria_id);
            form.append("planilla_id", this.info.planilla_id);

            let api = unujobs("post", `/work/${this.param}/descuento`, form);
            await api.then(res => {
                let { status, message, body } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});

                if (body) {
                    this.total = body.total;
                    this.total_neto = body.total_neto;
                    this.base = body.base;
                }

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