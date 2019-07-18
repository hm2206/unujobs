<template>
    <div class="mt-4">
        <div class="row">

            <div class="col-md-12">
                <div class="row align-items-center mb-3" v-for="(act, i) in tmp_actividades" :key="`act-${i}`">
                    <div class="col-md-3">
                        <input type="hidden" :name="`activities[${i}][0]`" v-model="act[0]">
                        <textarea class="form-control" :name="`activities[${i}][1]`" :placeholder="`Descripcion de la actividad N° ${i + 1}`" v-model="act[1]"></textarea>
                        <small class="text-danger" v-text="errors[`activity.${i}.1`] ? 'El campo es requerido' : null"></small>
                    </div>
                    <div class="col-md-3">
                        <label for="">Fecha Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" :name="`activities[${i}][2]`" v-model="act[2]">
                        <small class="text-danger" v-text="errors[`activity.${i}.2`] ? 'El campo debe ser de tipo fecha' : null"></small>
                    </div>
                    <div class="col-md-3">
                        <label for="">Fecha Final <small>(Opcional)</small></label>
                        <input type="date" class="form-control" v-model="act[3]" :name="`activities[${i}][3]`">
                    </div>
                    <div class="col-md-2">
                        <label for="">Responsable <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" v-model="act[4]" :name="`activities[${i}][4]`">
                        <small class="text-danger" v-text="errors[`activity.${i}.4`] ? 'El campo es obligatorio' : null"></small>
                    </div>
                    <div class="col-md-1">
                        <div class="btn-group mt-4">
                            <span class="btn btn-sm btn-dark cursor-pointer" v-on:click="add" v-if="i == (tmp_actividades.length - 1)">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="btn btn-sm btn-danger cursor-pointer" v-on:click="leave(i)" v-if="tmp_actividades.length > 1">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mt-4">
                <button class="btn btn-primary btn-block"><i class="fas fa-sync"></i> Actualizar</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .cursor-pointer {
        cursor: pointer;
    }
</style>


<script>
export default {
    props: ['errors', 'activities'],
    data() {
        return {
            tmp_actividades: [["","","","",""]]
        }
    },
    mounted() {
        if (this.activities.length > 0) {
            this.tmp_actividades = [];
            this.activities.map((obj, i) => {

                this.tmp_actividades.push([
                    obj[0], obj[1], obj[2], obj[3], obj[4]
                ]);

                return obj;
            });
        }
    },
    methods: {
        add(e) {
            e.preventDefault();
            
            if(this.tmp_actividades.length < 15) {
                this.tmp_actividades.push(["", "", "", "", ""])
            }else {
                alert("El limite ya fué alcanzado");
            }
        },
        leave(index) {
            this.tmp_actividades.splice(index, 1);
        }
    }
}
</script>
