<template>
    <div class="mt-3">

        <div class="row mb-2" v-for="(draw, i) in drawPerfiles" :key="i">
            <div class="col-md-6">
                <div class="form-group">
                    <b>
                        <span class="cursor-pointer mr-1 text-danger" v-on:click="destroy(i)" v-if="i > 0">
                            <i class="fas fa-trash"></i>
                        </span>
                        Titulo del Requisito N° <span v-text="i + 1"></span> 
                    </b>
                    <textarea :name="`requisitos[${i}][0]`" v-model="drawPerfiles[i][0]" class="form-control"></textarea>
                    <b class="text-danger" v-text="errors[`titulos.${i}`] ? 'Este campo es obligatorio' : null"></b>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row align-items-center" v-for="(body, b) in draw[1]" :key="`body-${b}`">
                    <div class="col-md-10">
                        <div class="form-group">
                            <b>Contenido N° <span v-text="b + 1"></span> del Requisito N° <span v-text="i + 1"></span></b>
                            <textarea :name="`requisitos[${i}][1][${b}]`" v-model="drawPerfiles[i][1][b]" class="form-control"></textarea>
                            <b class="text-danger" v-text="errors[`body.${i}.${b}`] ? 'Este campo es obligatorio' : null"></b>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="btn-group">
                            <span class="cursor-pointer btn btn-sm btn-dark" v-on:click="aggregarBody(i)" v-if="b == (draw[1].length - 1)">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="cursor-pointer btn btn-sm btn-danger" v-on:click="destroyBody(i, b)" v-if="b > 0">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <hr><hr>

        </div>
        
        <button class="btn btn-primary" v-on:click="aggregar"><i class="fas fa-plus"></i> Agregar</button>

        <div class="col-md-12">
            <hr>
        </div>

        <div class="mt-5 col-md-4" v-if="drawPerfiles.length > 0">
            <button class="btn btn-success btn-block">Guardar <i class="fas fa-save"></i></button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['questions', 'errors'],
    data() {
        return {
            drawPerfiles: [],
        };
    },
    mounted() {
        if (this.questions.length > 0) {
            this.questions.map((obj, i) => {
                let titulo = obj[0] ? obj[0] : "";
                let body = obj[1] ? obj[1] : [""];
                this.drawPerfiles.push([
                    titulo, 
                    body
                ]);
                return obj;                
            });
        }
    },
    methods: {
        aggregar(e) {
            e.preventDefault();
            if(this.drawPerfiles.length < 15) {
                this.drawPerfiles.push(["", [""]]);
            }else {
                alert("Solo se permite crear 15 Requisitos");
            }
        },
        destroy(index) {
            this.drawPerfiles.splice(index, 1);
        },
        aggregarBody(index) {
            if(this.drawPerfiles[index].length < 15) {
                this.drawPerfiles[index][1].push("");
            }else {
                alert("Solo se permite crear 15 Requisitos");
            }
        },
        destroyBody(index, element) {
            this.drawPerfiles[index][1].splice(element, 1);
        }
    }
}
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
</style>

