<template>
    <div>
        Ejemplo
    </div>
</template>

<script>

import jsPDF from 'jspdf';

export default {
    mounted() {
        this.createdPDF();
    },
    methods: {
        createdPDF() {
            let pdfName = 'test';
            var doc = new jsPDF();
            doc.text("Hello world", 10, 10);
            doc.save();
        }
    }
}
</script>