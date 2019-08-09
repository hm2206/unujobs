<template>
    <span>

        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false">
            <template slot="header">
                Liquidar al trabajador <b class="text-danger">{{ nombre_completo }}</b>
            </template>
            <template slot="content">
                <form class="card-body scroll-y" id="register-descuento" v-on:submit="submit">

                    <div class="form-group">
                        <label for="">Fecha de Ingreso <small class="text-danger">*</small></label>
                        <input type="date" class="form-control" name="key" v-model="fecha_de_inicio" :disabled="true">
                        <input type="hidden" name="fecha_de_ingreso" :value="fecha_de_inicio">
                        <small class="text-danger">{{ errors.fecha_de_inicio ? errors.fecha_de_inicio[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Fecha de Cese <small class="text-danger">*</small></label>
                        <input type="date" class="form-control" name="fecha_de_cese" v-model="cese">
                        <small class="text-danger">{{ errors.fecha_de_cese ? errors.fecha_de_cese[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Monto <small class="text-danger">*</small></label>
                        <input type="number" class="form-control" name="monto" v-model="monto">
                        <small class="text-danger">{{ errors.monto ? errors.monto[0] : '' }}</small>
                    </div>

                    <input type="hidden" name="work_id" :value="id">

                </form>
                <div class="card-footer text-right">
                    <button class="btn btn-success" :disabled="loader" v-on:click="submit">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </template>
        </modal>
    </span>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ["redirect", "id", "theme", "nombre_completo", "fecha_de_inicio"],
    data() {
        return {
            show: false,
            loader: false,
            monto: '',
            cese: '',
            errors: {},
        }
    },
    methods: {
        async submit(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('register-descuento'));

            this.loader = true;
            this.errors = {};

            let api = unujobs("post", "/liquidar", form);
            await api.then(async res => {

                let { status, message } = res.data; 

                if (status) {
                    await notify({icon: 'success', text: message});
                    location.href = this.redirect;
                }else {
                    await notify({icon: 'error', text: message});
                }

            }).catch(err => {
                this.errors = err.response.data.errors;
            });

            this.loader = false;

        }
    }
}
</script>

