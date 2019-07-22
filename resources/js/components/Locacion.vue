<template>
    <div class="row">

        <div class="col-md-4">
            <div class="form-group">
                <label for="">Departamento <b class="text-danger">*</b></label>
                <select v-model="departamento_id" name="departamento_id" 
                    class="form-control"
                    v-on:change="selectDepartamento"
                >
                    <option value="">----------</option>
                    <option :value="ubigeo.id"  v-for="(ubigeo, u) in ubigeos" :key="u" v-text="ubigeo.descripcion"></option>
                </select>
                <b class="text-danger">{{ errors['departamento_id'] ? errors['departamento_id'][0] : null }}</b>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="">Provincia <b class="text-danger">*</b></label>
                <select name="provincia_id" v-model="provincia_id" class="form-control"
                    v-on:change="selectProvincias"
                >
                    <option value="">----------</option>
                    <option :value="provincia.id" v-text="provincia.descripcion"
                        v-for="(provincia, pro) in provincias" :key="`pro-${pro}`"
                    ></option>
                </select>
                <b class="text-danger">{{ errors['provincia_id'] ? errors['provincia_id'][0] : null }}</b>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="">Distrito <b class="text-danger">*</b></label>
                <select name="distrito_id" class="form-control">
                    <option value="">----------</option>
                    <option :value="distrito.id" v-text="distrito.descripcion"
                        v-for="(distrito, dis) in distritos" :key="`dis-${dis}`"
                    ></option>
                </select>
                <b class="text-danger">{{ errors['distrito_id'] ? errors['distrito_id'][0] : null }}</b>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    props: ['errors', 'ubigeos'],
    data() {
        return {
            provincias: [],
            distritos: [],
            departamento_id: "",
            provincia_id: "",
            distrito_id: "",
            departamento: {},
            provincia: {}
        };
    },
    methods: {
        selectDepartamento(e) {
            let { name, value } = e.target;
            for(let dep of this.ubigeos) {
                if (dep.id == value) {
                    this.departamento = dep;
                    this.provincias = dep.departamentos;
                    break;
                }
            }
        },
        selectProvincias(e) {
            let { name, value } = e.target;
            for(let prov of this.provincias) {
                if (prov.id == value) {
                    this.provincia = prov;
                    this.distritos = prov.provincias;
                    break;
                }
            }
        }
    }
}
</script>