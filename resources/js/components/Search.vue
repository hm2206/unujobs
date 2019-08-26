<template>
    <li class="nav-item no-arrow mx-1">
        <a :class="`nav-link ${className}`" href="#">
            <input type="text" class="form-control mr-2" placeholder="Buscar..." v-if="isSearch" v-on:enter="search">
            <i class="fas fa-times text-danger" v-if="isSearch" v-on:click="isSearch = false"></i>
            <i class="fas fa-search" v-on:click="isSearch = true" v-else></i>
        </a>
    </li>
</template>

<script>
export default {
    props: ['className'],
    data() {
        return {
            isSearch: false
        }
    },
    methods: {
        search(e) {
            alert('listo');
        }
    }
}
</script>
