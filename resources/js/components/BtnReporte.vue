<template>
    <div class="btn-group">
        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        
        <modal @close="show = false" col="col-md-10" :show="show" height="95vh" style="padding-top: 10px;">
            <template slot="header">
                Reportes del {{ cronograma ? cronograma.mes : '' }} del {{ cronograma ? cronograma['año'] : '' }}
            </template>
            <template slot="content">
                <div class="card-body p-relative scroll-y">
                
                    <ul class="nav nav-tabs">
                        <li class="nav-item" v-for="(item, i) in items" :key="`item-${i}`">
                            <a href="#" 
                                :class="`nav-link ${item.active ? 'active' : ''}`"
                                v-on:click="seleccionar($event, item)"
                            >
                                {{ item.text }}
                            </a>
                        </li>
                    </ul>

                    <component :is="current" v-if="!loader"
                        :report="report"
                        :cronograma="cronograma"
                        :metas="metas"
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
import { setTimeout } from 'timers';

export default {
    components: {
        'rpt-general': RptGeneral,
        'rpt-meta': RptMeta,
        'rpt-boleta': RptBoleta,
        'rpt-pago': RptPago,
        'rpt-afp': RptAfpNet,
        'rpt-descuento': RptDescuento,
        'rpt-planilla': RptPlanilla
    },
    props: ["theme", 'cronograma', 'type_reports', 'metas'],
    mounted() {

        var componentes = [
            'rpt-general', 'rpt-meta', 'rpt-boleta', 'rpt-pago',
            'rpt-afp', 'rpt-descuento', 'rpt-planilla'
        ];

        this.type_reports.filter((e, iter) => {
            this.items.push({
                id: e.id,
                text: e.descripcion,
                active: iter == 0 ? true : false,
                component: componentes[iter],
                btn: false,
                body: e
            });

            if (iter == 0) {
                this.report = e
            }

        });
    },
    data() {
        return {
            show: false,
            loader: false,
            current: 'rpt-general',
            items: [],
            report: {},
            send: false,
            btn: false
        }
    },
    methods: {
        async seleccionar(e, item) {
            e.preventDefault();
            if (!this.send) {
                this.loader = true;
                await this.items.map((i) => {
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
