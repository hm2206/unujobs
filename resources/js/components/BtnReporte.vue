<template>
    <div class="btn-group">
        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        
        <modal @close="show = false" col="col-md-12" :show="show" height="95vh" style="padding-top: 10px;">
            <template slot="header">
                Reportes del {{ cronograma ? cronograma.mes : '' }} del {{ cronograma ? cronograma['año'] : '' }}
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                
                    <ul class="nav nav-tabs">
                        <li class="nav-item" v-for="(item, i) in type_reports" :key="`item-${i}`">
                            <a href="#" 
                                :class="`nav-link ${item.active ? 'active' : ''}`"
                                v-on:click="seleccionar($event, item)"
                            >
                                {{ item.text }}
                            </a>
                        </li>
                    </ul>

                    <component :is="current" v-if="!loader"
                        :cronograma="cronograma"
                        :metas="metas"
                        :cargos="cargos"
                    >
                    </component>

                    <div class="col-md-12 mt-3" v-if="loader">
                        <div class="text-center">
                            <div class="spinner-border text-warning"></div>
                        </div>
                    </div>

                </div>

                <div :class="`card-footer ${send ? 'text-center' : 'text-right'}`" v-if="btn">
                    <button class="btn btn-primary"
                        v-if="!send"
                        v-on:click="btnPress"
                    >
                            <i class="fa fa-sync"></i> Actualizar
                    </button>

                    <div class="text-primary spinner-border" v-if="send"></div>
                </div>

            </template>
        </modal>
    </div>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

import WorkRemuneracion from './WorkRemuneracion';
import RptGeneral from './RptGeneral';
import RptMeta from './RptMeta';
import RptBoleta from './RptBoleta';
import RptPago from './RptPago';
import RptAfpNet from './RptAfpNet';
import RptDescuento from './RptDescuento';
import RptPlanilla from './RptPlanilla';
import RptEjecutar from './RptEjecutar';
import RptPersonal from './RptPersonal';
import RptAporte from './RptAporte';
import { setTimeout } from 'timers';

export default {
    components: {
        'rpt-general': RptGeneral,
        'rpt-meta': RptMeta,
        'rpt-boleta': RptBoleta,
        'rpt-pago': RptPago,
        'rpt-afp': RptAfpNet,
        'rpt-descuento': RptDescuento,
        'rpt-aporte': RptAporte,
        'rpt-planilla': RptPlanilla,
        'rpt-ejecutar': RptEjecutar,
        'rpt-personal': RptPersonal,
    },
    props: ["theme", 'cronograma', 'metas', 'cargos'],
    data() {
        return {
            show: false,
            loader: false,
            current: 'rpt-general',
            items: [],
            type_reports: [
                { id: 1, text: 'Report General', component: 'rpt-general', active: true},
                { id: 2, text: 'Report. Meta', component: 'rpt-meta', active: false },
                { id: 3, text: 'Boletas', component: 'rpt-boleta', active: false },
                { id: 4, text: 'Cuentas y Cheques', component: 'rpt-pago', active: false },
                { id: 5, text: 'AFP', component: 'rpt-afp', active: false },
                { id: 6, text: 'Descuentos', component: 'rpt-descuento', active: false },
                { id: 7, text: 'Aportaciones', component: 'rpt-aporte', active: false },
                { id: 8, text: 'Planilla', component: 'rpt-planilla', active: false },
                { id: 9, text: 'Ejec. Planilla', component: 'rpt-ejecutar', active: false },
                { id: 10, text: 'Relación de Personal', component: 'rpt-personal', active: false },
            ],
            send: false,
            btn: false
        }
    },
    methods: {
        async seleccionar(e, item) {
            e.preventDefault();
            if (!this.send) {
                this.loader = true;
                await this.type_reports.map((i) => {
                    if (item.id == i.id) {
                        i.active = true;
                    }else {
                        i.active = false;
                    }
                });

                this.loader = false;
                this.current = item.component;
                this.btn = item.btn;
                this.report = item.body;
            }
        },
        btnPress(e) {
            this.send = true;
        },
        setLoader(e) {
            this.send = false;
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
