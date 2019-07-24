<template>
    <select class="form-control" v-on:change="change" :name="name" :id="name">
        <option value="">Seleccionar...</option>
        <option :value="dato[value]" v-for="(dato, d) in datos" :key="`${value}-${d}`" 
            v-text="dato[text]" :selected="request == dato[value]"
        >
        </option>
    </select>
</template>

<script>
export default {
    props: ['datos', 'text', 'value', 'name', 'for', 'request'],
    data() {
        return {
            
        }
    },
    methods: {
        change(e) {
            let { value, name } = e.target;
            if (value != "") {
                let formulario = document.getElementById(this.for);
                formulario.submit();
            }
        }
    }
}
</script>