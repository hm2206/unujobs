<template>
    <div class="mt-3">

        <div class="mt-3 row align-items-center" v-for="(base, b) in tmp_bases" :key="`base-${b}`">
            <div class="col-md-10">
                <textarea name="bases[]" class="form-control" v-model="tmp_bases[b]" :placeholder="`Escriba la base N° ${b + 1}`"></textarea>
                <b class="text-danger" v-text="errors[`bases.${b}`] ? 'EL campo es obligatorio' : null"></b>
            </div>
            <div class="col-md-2">
                <div class="btn-group">
                    <button class="btn btn-sm btn-dark" v-if="b == tmp_bases.length - 1" v-on:click="aggregar"><i class="fas fa-plus"></i></button>
                    <span class="btn btn-sm btn-danger cursor-pointer" v-if="b != 0" v-on:click="destroy(b)"><i class="fas fa-times"></i></span>
                </div>
            </div>
        </div>




    </div>
</template>

<script>
export default {
    props: ["errors", "bases"],
    data() {
        return {
            tmp_bases: [""]
        }
    },
    mounted() {
        if(this.bases.length > 0) {
            this.tmp_bases = [];
            this.bases.map((obj, i) => {
                this.tmp_bases.push(obj);
            });
        }
    },
    methods: {
        aggregar(e) {
            e.preventDefault();

            if(this.tmp_bases.length < 10) {
                this.tmp_bases.push("");
            }else {
                alert("Solo se permiten 10 bases como máximo");
            }
        },
        destroy(index) {
            this.tmp_bases.splice(index, 1);
        }
    },
}
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
</style>

