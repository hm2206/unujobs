<template>
    <select id="" class="form-control">
        <option value="" :v-for="(dato in this.props.datos)" :key="datos">

        </option>
        <option value="">Selecionar</option>
    </select>
</template>


<script>
export default {
    props: {
        datos : {
            type: Array,
            default: []
        }
    }
}
</script>
