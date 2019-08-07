<template>
    <div class="btn-group">
        <button :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        <ventana opacity="0.3" index="90" v-if="show">
            <div class="col-md-10">
                <modal @click="close">
                    <template slot="header">
                        Lista de boletas de 
                        <i class="fas fa-arrow-right text-danger"></i> 
                        <span v-text="nombre_completo"></span>
                    </template>
                    <template slot="content" class="p-relative">
                        <div class="card-body">
                             <form class="table-responsive" method="POST" :action="url"
                                v-on:submit="loader = true"
                             >
                                <input type="hidden" name="_token" :value="token">
                                <table class="table">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>Seleccionar</th>
                                            <th>Planilla</th>
                                            <th>Observación</th>
                                            <th class="text-center">Adicional</th>
                                            <th class="text-center">Mes</th>
                                            <th class="text-center">Año</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(cronograma, c) in cronograma.data" :key="c">
                                            <td>
                                                <input type="checkbox" :value="cronograma.id" name="cronogramas[]" 
                                                    v-on:change="validate"
                                                >
                                            </td>
                                            <td>{{ cronograma.planilla ? cronograma.planilla.descripcion : '' }}</td>
                                            <td>{{ cronograma.observacion }}</td>
                                            <td class="text-center">
                                                <span :class="`btn btn-sm btn-${cronograma.adicional ? 'primary' : 'danger'}`">
                                                    {{ cronograma.adicional ? cronograma.numero : 'No' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="btn btn-sm btn-dark">
                                                    {{ cronograma.mes }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="btn btn-sm btn-dark">
                                                    {{ cronograma['año'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="text-center" v-if="loader">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                            <td v-if="!loader && cronograma.total == 0" 
                                                class="text-center" colspan="6"
                                            >
                                                No hay registros disponibles
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <button class="btn-fixed btn btn-success btn-circle btn-lg" 
                                    :disabled="loader"
                                    v-if="cronograma.total > 0 && count > 0"
                                >
                                    <i class="fa fa-sync"></i>
                                </button>

                            </form>
                        </div>
                    </template>
                </modal>
            </div>
        </ventana>
    </div>
</template>

<script>

import { unujobs } from '../services/api';

export default {
    props: ["theme", 'param', "url", "nombre_completo", "token"],
    data() {
        return {
            show: false,
            loader: true,
            cronograma: {},
            count: 0
        }
    },
    watch: {
        show(nuevo, old) {
            if (nuevo) {
                this.getBoletas();
            }
        }
    },
    methods: {
        close() {
            this.show = false;
        },
        async getBoletas() {
            this.loader = true;
            let api = unujobs("get", `/boleta/${this.param}`);
            await api.then(res => {
                this.cronograma = res.data;
            }).catch(err => {
                console.log(err);
            })

            this.loader = false;
        },
        validate(e) {
            let { checked } = e.target;
            this.count = checked ? this.count + 1 : this.count - 1;
        },
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
</style>
