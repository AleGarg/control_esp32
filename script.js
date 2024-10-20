function mostrar(elemento){
    alert('anduvo');
    elemento.classList.remove("displaynone");

    // var iframe = document.getElementById('twitch-stream');
    // Aquí cargas el stream en el iframe solo cuando el usuario lo permite
    elemento.src = "https://player.twitch.tv/?channel=rubius&parent=localhost&autoplay=true";
}