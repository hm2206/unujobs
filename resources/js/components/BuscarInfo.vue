<template>
    <span>  
        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <modal :show="show" @close="show = false" height="80vh"  col="col-md-9">
            <template slot="header">
                Buscar Trabajador
            </template>
            <template slot="content">
                <form class="card-body scroll-y" id="register-descuento" v-on:submit="buscar">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" v-model="search" placeholder="Ingrese un nombre para realizar la busqueda...">   
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-info" v-if="!loader">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <div class="text center" v-if="loader">
                                        <div class="spinner-border text-primary"></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="col-md-12" v-for="(result, res) in resultados" :key="`rest-${res}`">
                            <div class="row">
                                <div class="col-md-6">{{ result.work ? result.work.nombre_completo : '' }}</div>
                                <div class="col-md-2">{{ result.work ? result.work.numero_de_documento : '' }}</div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-danger">
                                        {{ result.categoria ? result.categoria.nombre : '' }}
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="#" v-on:click="seleccionar($event, result.id)" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Seleccionar
                                    </a>
                                </div>
                            </div>
                            <hr/>
                        </div>
                    </div>
                </form>
            </template>
        </modal>
    </span>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ["leave", "theme"],
    data() {
        return {
            show: false,
            search: '',
            resultados: [],
            loader: false,
        }
    },
    watch: {
        leave(nuevo) {
            if (nuevo) {
                this.search = "";
                this.resultados = [];
            }
        }
    },
    methods: {
        async buscar(e) {
            e.preventDefault();
            this.loader = true;
            let api = unujobs("get", `/work?query_search=${this.search}`);
            await api.then(res => {
                this.resultados = res.data;
            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal :('});
            }); 
            this.loader = false;
        },
        async seleccionar(e, id) {
            e.preventDefault();
            await this.$emit('find', id);
            this.show = false;
        }
    }
}
</script>

