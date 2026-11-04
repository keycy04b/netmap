document.addEventListener("DOMContentLoaded", () => {
    console.log("¡NetMap iniciado correctamente!");
    
    cargarCatalogos();
    cargarEquipos();
});

async function cargarCatalogos() {
    try {
        let respuesta = await fetch("api/obtener_catalogos.php");
        let datos = await respuesta.json();
        console.log("Catálogos recibidos:", datos);
    } catch (error) {
        console.error("Error cargando catálogos:", error);
    }
}

async function cargarEquipos() {
    try {
        let respuesta = await fetch("api/obtener_equipos.php");
        let equipos = await respuesta.json();
        
        console.log("Equipos recibidos desde la base de datos:", equipos);
        
        // Llamamos a la función encargada de pintar los equipos en la pantalla
        dibujarEquipos(equipos);
    } catch (error) {
        console.error("Error cargando equipos:", error);
    }
}

// Función que toma la lista de equipos y los dibuja en el lienzo
function dibujarEquipos(equipos) {
    // 1. Buscamos el contenedor principal (el lienzo) en nuestro HTML
    const lienzo = document.getElementById("lienzo-red");
    
    // 2. Recorremos cada equipo que viene de la base de datos con un ciclo
    equipos.forEach(eq => {
        // Creamos una cajita (div) de forma dinámica en la memoria del navegador
        let tarjeta = document.createElement("div");
        tarjeta.className = "equipo-card";
        
        // 3. Asignamos su posición exacta usando las coordenadas X e Y guardadas
        tarjeta.style.left = eq.posicion_x + "px";
        tarjeta.style.top = eq.posicion_y + "px";
        
        // 4. Rellenamos la cajita con el nombre del equipo, su tipo y su IP
        tarjeta.innerHTML = `
            <div class="equipo-titulo">${eq.nombre_equipo}</div>
            <div class="equipo-sub">${eq.nombre_tipo} • ${eq.direccion_ip || 'Sin IP'}</div>
        `;
        
        // 5. Insertamos la tarjeta terminada dentro del lienzo visual
        lienzo.appendChild(tarjeta);
    });
}

