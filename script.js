document.addEventListener("DOMContentLoaded", function () {
    let valorConcatenado; // Variable para almacenar el valor concatenado

    setInterval(function () {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "control_esp32.php", true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Parsear la respuesta JSON
                const data = JSON.parse(xhr.responseText);

                // Actualizar solo el valor concatenado
                var valorElement = document.getElementById("valor_concatenado");
                if (valorElement) {
                    valorElement.innerText = data.valor_concatenado; // Actualiza solo el valor concatenado
                }


                var iframe = document.getElementById("twitch-stream");
                

                // Condición para cambiar el src del iframe si el valor es 8
                if (data.valor_concatenado === "8") {
                    iframe.src = "https://player.twitch.tv/?channel=rubius&parent=localhost&autoplay=true";
                }
                if (data.valor_concatenado === "69") {
                    // <iframe width="1366" height="480" src="https://www.youtube.com/embed/cpLrWyAaC_U" title="Gol de Juan Fernando Quintero en la final de la Copa Libertadores" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    
                    iframe.src = "https://www.youtube.com/embed/cpLrWyAaC_U";
                }
            }
        };
        xhr.send();
    }, 1000); // Ajusta el intervalo según sea necesario
});

