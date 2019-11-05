<template>
    <span>

        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false">
            <template slot="header">
                Agregar Descuento
            </template>
            <template slot="content">
                <form class="card-body scroll-y" style="font-size: 16px;" id="agregar-descuentos" v-on:submit="send">

                    <div class="form-group">
                        <label for="">Tipo Descuento <small class="text-danger">*</small></label>
                        <select name="type_descuento_id" class="form-control">
                            <option value="">Seleccionar</option>
                            <option :value="type.id" v-for="(type, ty) in type_descuentos" :key="ty">
                                {{ type.descripcion }}
                            </option>
                        </select>
                        <small class="text-danger">{{ errors.key ? errors.key[0] : '' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="">Monto <small class="text-danger">*</small></label>
                        <input type="number" class="form-control" name="monto" value="0">
                        <small class="text-danger">{{ errors.descripcion ? errors.descripcion[0] : '' }}</small>
                    </div>

                </form>
                <div class="card-footer text-right">
                    <button class="btn btn-success" v-on:click="send" v-if="!loader">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <div class="text-center" v-else>
                        <div class="spinner-border text-primary"></div>
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
    props: ["redirect", "theme", "cronograma"],
    data() {
        return {
            type_descuentos: [],
            show: false,
            errors: {},
            loader: false,
            edit: false,
            checked: false
        }
    },
    mounted() {
        this.getTypeDescuentos();
    },
    methods: {
        getTypeDescuentos() {
            let api = unujobs('get', '/type_descuento');
            api.then(res => {
                this.type_descuentos = res.data.filter(obj => obj.base == 0);
            }).catch(err => {
                console.log(err.message);
            })
        },
        async send(e) {
            e.preventDefault();
            this.loader = true;
            let answer = await confirm('¿Está seguro en continuar?');
            if (answer) {
                const form = new FormData(document.getElementById('agregar-descuentos'));
                let api = unujobs('post', `/cronograma/${this.cronograma.id}/crear-descuentos`, form);
                await api.then(res => {
                    let { status, message } = res.data;
                    let icon = status ? 'success' : 'error';
                    notify({ icon, text: message });
                }).catch(err => {
                    notify({ icon: 'error', text: 'Algo salió mal' });
                });
            }
            this.loader = false;
        }
    }
}
</script>

