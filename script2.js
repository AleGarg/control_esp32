document.addEventListener("DOMContentLoaded", function () {
    function obtenerDatos() {
        fetch("control_esp32.php")
            .then(response => response.json())
            .then(data => {
                document.getElementById("ultimo-valor").textContent = data.valor1 || 'No hay datos aún.';
                document.getElementById("fecha-ultimo").textContent = data.fecha1 ? new Date(data.fecha1).toLocaleTimeString() : 'No disponible';
                
                document.getElementById("anteultimo-valor").textContent = data.valor2 || 'No hay datos aún.';
                document.getElementById("fecha-anteultimo").textContent = data.fecha2 ? new Date(data.fecha2).toLocaleTimeString() : 'No disponible';
                
                document.getElementById("diferencia-segundos").textContent = data.diferencia_en_segundos || 'No disponible';
                document.getElementById("valor-concatenado").textContent = data.valor_concatenado || 'No hay concatenación';
            })
            .catch(error => console.error("Error al obtener datos:", error));
    }
    
    // Llamada inicial y luego intervalos para obtener datos sin duplicar contenido
    obtenerDatos();
    setInterval(obtenerDatos, 1000);
});