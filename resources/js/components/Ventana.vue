<template>
    <div class="ventana" :style="`background: rgba(0, 0, 0, ${opacity}); z-index: ${index};`">
        <div class="row justify-content-center align-items-center h-100 pb-2">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        opacity: {
            type: String,
            default: "0.5"
        },
        index: {
            type: String,
            default: "100"
        },
    }
}
</script>


<style scoped>
    .ventana {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0px;
        left: 0px;
        overflow: hidden;
        padding: 2em 1em;
    }
</style>

