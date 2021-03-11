<template>
    <button :class="`btn-fixed btn ${theme} btn-${size}`" 
        v-if="show"
        v-on:click="evento"
    >
        <slot></slot>
    </button>
</template>


<script>
export default {
    props: {
        theme: {
            type: String,
            default: "btn-success btn-circle"
        },
        size: {
            type: String,
            default: "lg"
        },
        show: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        evento(e) {
            this.$emit("click", e);
        }
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

