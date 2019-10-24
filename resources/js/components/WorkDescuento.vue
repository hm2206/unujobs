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
    props: ['cronograma', 'send', 'history'],
    data() {
        return {
            descuentos: [],
            loader: false,
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
        async history() {
            await this.getDescuentos();
        },
        send(nuevo) {
            if (nuevo) {
                this.submit();
            }
        }
    },
    methods: {
        async getDescuentos() {

            let  api = unujobs('get', `/historial/${this.history.id}/descuento`);
            this.tmp_descuentos = [];

            await api.then(async res => {
                let { descuentos, total_neto, base, total_desct } = res.data;
                this.descuentos = descuentos;
                this.total = total_desct;
                this.base = base;
                this.total_neto = total_neto;
                this.descuentos.filter(des => {
                    this.tmp_descuentos.push(des.monto);
                });

            }).catch(err => {
                console.log('algo salio mal al obtener los descuentos');
            });

        },
        async submit(e) {

            if (e) {
                e.preventDefault();
            }

            let payload = [];
            const form = new FormData;
            this.descuentos.filter((des, iter) => {
                let monto = this.tmp_descuentos[iter];
                payload.push({ id: des.id, monto });
            });

            this.loader = true;
            form.append("_method", "PUT");
            form.append('descuentos', JSON.stringify(payload));

            let api = unujobs("post", `/type_descuento/${this.history.id}/all`, form);
            await api.then(res => {
                let { status, message, total_desct, total_neto } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});

                this.total = total_desct
                this.total_neto = total_neto;

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