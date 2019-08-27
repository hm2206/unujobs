<template>
    <form class="card-body">
        <div class="row">

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Apellido Paterno</b></small>
                    <input type="text" class="form-control" v-model="work.ape_paterno">
                    <small class="text-danger">{{ errors.ape_paterno ? errors.ape_paterno[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Apellido Materno</b></small>
                    <input type="text" class="form-control" v-model="work.ape_materno" name="ape_materno">
                    <small class="text-danger">{{ errors.ape_materno ? errors.ape_materno[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Nombres</b></small>
                    <input type="text" class="form-control" v-model="work.nombres" name="nombres">
                    <small class="text-danger">{{ errors.nombres ? errors.nombres[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>N° Documento</b></small>
                    <input type="text" class="form-control" v-model="work.numero_de_documento" name="numero_de_documento">
                    <small class="text-danger">{{ errors.numero_de_documento ? errors.numero_de_documento[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Fecha de Nacimiento</b></small>
                    <input type="date" class="form-control" v-model="work.fecha_de_nacimiento" name="fecha_de_nacimiento">
                    <small class="text-danger">{{ errors.fecha_de_nacimiento ? errors.fecha_de_nacimiento[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Email</b></small>
                    <input type="email" class="form-control" v-model="work.email" name="email">
                    <small class="text-danger">{{ errors.email ? errors.email[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Dirección</b></small>
                    <input type="text" class="form-control" v-model="work.direccion" name="direccion">
                    <small class="text-danger">{{ errors.direccion ? errors.direccion[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Profesión</b></small>
                    <input type="text" class="form-control" v-model="work.profesion" name="profesion">
                    <small class="text-danger">{{ errors.profesion ? errors.profesion[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>N° Teléfono</b></small>
                    <input type="tel" class="form-control" v-model="work.phone" name="phone">
                    <small class="text-danger">{{ errors.phone ? errors.phone[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Fecha de ingreso</b></small>
                    <input type="text" class="form-control" v-model="work.fecha_de_ingreso" name="fecha_de_ingreso">
                    <small class="text-danger">{{ errors.fecha_de_ingreso ? errors.fecha_de_ingreso[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Sexo</b></small>
                    <select name="sexo" v-model="work.sexo" class="form-control">
                        <option value="1">Masculino</option>
                        <option value="0">Femenino</option>
                    </select>
                    <small class="text-danger">{{ errors.sexo ? errors.sexo[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>N° Essalud Autogenerable</b></small>
                    <input type="text" class="form-control" v-model="work.numero_de_essalud" name="numero_de_essalud">
                    <small class="text-danger">{{ errors.numero_de_essalud ? errors.numero_de_essalud[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>Banco</b></small>
                    <select name="banco_id" v-model="work.banco_id" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option :value="banco.id" v-for="(banco, ban) in bancos" :key="`banco-${ban}`">
                            {{ banco.nombre }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small><b>N° Cuenta</b></small>
                    <input type="text" class="form-control" v-model="work.numero_de_cuenta" name="numero_de_cuenta">
                    <small class="text-danger">{{ errors.numero_de_cuenta ? errors.numero_de_cuenta[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small class="form-control-label"><b>AFP</b></small>
                    <select name="afp_id" v-model="work.afp_id" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option :value="afp.id" v-for="(afp, index) in afps" :key="`afp-${index}`">
                            {{ afp.nombre }}
                        </option>
                    </select>
                    <small class="text-danger">{{ errors.afp_id ? errors.afp_id[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small class="form-control-label"><b>Tipo de AFP</b></small>
                    <select name="type_afp" v-model="work.type_afp" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option value="flujo">Flujo</option>
                        <option value="mixta">Mixta</option>
                    </select>
                    <small class="text-danger">{{ errors.type_afp ? errors.type_afp[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small class="form-control-label"><b>Fecha de Afiliación</b></small>
                    <input type="date" class="form-control" v-model="work.fecha_de_afiliacion" name="fecha_de_afiliacion">
                    <small class="text-danger">{{ errors.fecha_de_afiliacion ? errors.fecha_de_afiliacion[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small class="form-control-label"><b>N° CUSSP</b></small>
                    <input type="text" class="form-control" v-model="work.numero_de_cussp" name="numero_de_cussp">
                    <small class="text-danger">{{ errors.numero_de_cussp ? errors.numero_de_cussp[0] : '' }}</small>
                </div>
            </div>
            

            <div class="col-md-3">
                <div class="form-group">
                    <small class="form-control-label"><b>Accidentes de Trabajo</b></small>
                    <select name="type_afp" v-model="work.accidentes" class="form-control">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                    <small class="text-danger">{{ errors.accidentes ? errors.accidentes[0] : '' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <small class="form-control-label"><b>Cheque</b></small>
                     <select name="type_afp" v-model="work.cheque" class="form-control">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                    <small class="text-danger">{{ errors.cheque ? errors.cheque[0] : '' }}</small>
                </div>
            </div>

        </div>
    </form>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['work', 'objetos'],
    data() {
        return {
            afps: [],
            bancos: [],
            errors: {}
        };
    },
    mounted() {
        this.bancos = this.objetos.bancos;
        this.afps = this.objetos.afps;
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