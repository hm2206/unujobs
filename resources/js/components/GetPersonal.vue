<template>
    <div>
        Lista de trabajadores

        <ul>
            <li v-for="(book, b) in books" :key="b">{{ book.titulo }}</li>
        </ul>
    </div>
</template>

<script>

import gql from 'graphql-tag'

export default {
    apollo: {
        books: gql`
            query {
                books {
                id
                titulo
                descripcion
            }
        }
        `
    }
}
</script>