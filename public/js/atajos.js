/*====================================================================================================================
=                                                funciones teclas rapidas                                            =
====================================================================================================================*/
document.addEventListener('DOMContentLoaded', function() {
    let currentIndex = -1; // Mover fuera del keydown para que no se reinicie

    // Captura eventos de teclado
    document.addEventListener('keydown', function(event) {
        if (event.key === 'F3' || event.keyCode === 114) { // Enfoque de buscador producto
            event.preventDefault();
            document.getElementById('bproducto').focus();
        }

        if (event.key === 'F4' || event.keyCode === 115) { // Enfoque de buscador código barra
            event.preventDefault();
            document.getElementById('codbarra').focus();
        }

        if (event.key === 'F8' || event.keyCode === 119) { // Cancelar venta
            event.preventDefault();
            location.reload();
        }

        if (event.key === 'Enter') { // Finalizar venta
            event.preventDefault();
            let urlActual = window.location.href;
            let segmentos = urlActual.split('/');
            envioVenta(urlActual.replace(segmentos[4], 'guardar'));
        }

        if (event.ctrlKey && event.shiftKey && (event.key === 'c' || event.keyCode === 67)) { // Agregar cliente
            event.preventDefault();
            mostrarModal(base_url + 'cliente/buscador/V', 'bdatos', 'Buscar Cliente');
        }

        // Seleccionar todas las filas de la tabla
        const rows = Array.from(document.querySelectorAll('#tblproducto tr'));

        // Función para resaltar la fila actual
        function highlightRow(index) {
            rows.forEach((row, i) => {
                row.classList.toggle('highlight', i === index);
            });
            const button = rows[index].querySelector('.punidad');
            if (button) {
                button.focus();
            }
        }

        if (event.key === 'F7') {
            event.preventDefault();
            document.getElementById('bproducto').blur();
            currentIndex = 0; // Resaltar la primera fila al presionar F7
            highlightRow(currentIndex);
        } else if (event.key === 'ArrowDown') {
            event.preventDefault();
            currentIndex = (currentIndex + 1) % rows.length; // Mover hacia abajo
            highlightRow(currentIndex);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            currentIndex = (currentIndex - 1 + rows.length) % rows.length; // Mover hacia arriba
            highlightRow(currentIndex);
        } else if (event.key === 'F9') {
            event.preventDefault();
            const button = rows[currentIndex].querySelector('.punidad');
            if (button) {
                button.click(); // Ejecutar acción del botón dentro de la fila resaltada
            }
        }
    });
});
