<template>
    <span>
        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>
        <modal :show="show" @close="close" col="col-md-6" height="40vh">
            <template slot="header">
                Reporte de Abono
                <i class="fas fa-arrow-right text-danger"></i>
                <span v-text="cronograma.planilla ? cronograma.planilla.descripcion : ''"></span>
                <i class="fas fa-arrow-right text-danger" v-if="cronograma.adicional"></i>
                <span v-if="cronograma.adicional">Adicional</span>
                <span v-if="cronograma.adicional" 
                    v-text="cronograma.numero" 
                    class="btn btn-sm btn-primary"
                ></span>
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">

                    <form action="" class="row justify-content-center" id="register-reporter">
                        <div class="col-md-4 text-center">
                            <input type="checkbox" name="cheque" v-on:change="validate"> Cheque
                        </div>
                        <div class="col-md-4 text-center">
                            <input type="checkbox" name="cuenta" v-on:change="validate"> Cuenta
                        </div>
                    </form>

                    <btn-validate 
                        :show="hasSelect"
                        @click="add"
                        size="md"
                    >
                        <i class="fas fa-download"></i>
                    </btn-validate>
                </div>
            </template>
        </modal>
    </span>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['theme', "cronograma"],
    data() {
        return {
            show: false,
            loader: false,
            count: 0
        }
    },
    methods: {
        validate(e) {

            let { name, value, checked } = e.target;

            this.count = checked ? this.count + 1 : this.count - 1;

        },
        async add(e) {

            if (e) {
                e.preventDefault();
            }

            this.loader = true;

            const form = new FormData(document.getElementById('register-reporter'));

            let api = unujobs("post", `/export/cuenta-cheque/${this.cronograma.id}`, form);

            await api.then(async res => {

                let { status, message } = res.data;

                let icon = status ? "success" : "error";

                await notify({icon: icon, text: message});

            }).catch(err => {
                notify({icon: 'error', text: err.message});
            })

            this.loader = false;
        },
        close() {
            this.count = 0;
            this.show = false;
        },
    },
    computed: {
        hasSelect() {
            return this.count > 0;
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

