<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Configuración de la conexión a la base de datos
$servername = "localhost"; // Cambia esto si tu servidor es diferente
$username = "root"; // Cambia esto si tienes otro usuario
$password = ""; // Cambia esto si tienes contraseña
$dbname = "proyecto_control"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la cantidad de filas en la tabla
$result = $conn->query("SELECT COUNT(*) AS total FROM datos_recibidos");
$row = $result->fetch_assoc();
$total_filas = $row['total'];

$fecha_actual = date('Y-m-d H:i:s');

// Manejar la solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data'] ?? 'No data received';

    if($data == 'PREV'){
        echo "<h1>
            Retrocediendo
        </h1>";
        // system("cmd /c C:/control/ejecutar.bat");
        system("cmd /c C:/xampp/htdocs/control_esp32/iratras.bat");
    }
    if($data == 'CH_mas'){
        $data = "CH+";
    }
    if($data == 'VOL_mas'){
        $data = "VOL+";
    }

    // Si el valor recibido no es numérico, vaciar la tabla y guardarlo como primer valor
    if (!is_numeric($data)) {
        $conn->query("TRUNCATE TABLE datos_recibidos");
        $stmt = $conn->prepare("INSERT INTO datos_recibidos (valor, fecha) VALUES (?, ?)");
        $stmt->bind_param("ss", $data, $fecha_actual);
        $stmt->execute();
        $stmt->close();
        echo "Dato no numérico recibido, se ha vaciado la tabla y se ha cargado el nuevo valor.";
        $valor_concatenado = $data; // Guardar como valor concatenado
    } else {
        // Caso 1: Si la tabla está vacía, agregar el nuevo dato
        // Caso 2: Si hay 1 solo dato, agregar el segundo
        if ($total_filas == 0 || $total_filas == 1) {
            $stmt = $conn->prepare("INSERT INTO datos_recibidos (valor, fecha) VALUES (?, ?)");
            $stmt->bind_param("ss", $data, $fecha_actual);
            $stmt->execute();
            echo "DATO CARGADO TIO";
        }
        // Caso 3: Si hay 2 datos, borrar ambos y agregar el nuevo dato
        elseif ($total_filas >= 2 ) {
            // Borrar los 2 registros actuales
            $conn->query("TRUNCATE TABLE datos_recibidos");

            // Insertar el nuevo dato
            $stmt = $conn->prepare("INSERT INTO datos_recibidos (valor, fecha) VALUES (?, ?)");
            $stmt->bind_param("ss", $data, $fecha_actual);
            $stmt->execute();
            echo "Se han borrado los datos anteriores y se añadió un nuevo dato.";
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT valor, fecha, 
                 TIMESTAMPDIFF(SECOND, 
                               (SELECT fecha FROM datos_recibidos ORDER BY id DESC LIMIT 1 OFFSET 1), 
                               (SELECT fecha FROM datos_recibidos ORDER BY id DESC LIMIT 1)) 
                 AS diferencia_en_segundos 
          FROM datos_recibidos 
          ORDER BY id DESC 
          LIMIT 2");
$ultimos_datos = $result->fetch_all(MYSQLI_ASSOC);

$valor1 = $ultimos_datos[0]['valor'] ?? '';
$fecha1 = $ultimos_datos[0]['fecha'] ?? '';

$valor2 = $ultimos_datos[1]['valor'] ?? '';
$fecha2 = $ultimos_datos[1]['fecha'] ?? '';

$diferencia_en_segundos = $ultimos_datos[0]['diferencia_en_segundos'] ?? 'Ninguna, 1er valor';

// Calcular la diferencia de tiempo y concatenar si ambos son numéricos y si la diferencia es menor a 10 segundos
if ($fecha1 && $fecha2) {
    if (is_numeric($valor1) && is_numeric($valor2)) {
        if ($diferencia_en_segundos < 5) {
            $valor_concatenado = $valor2 . $valor1;
        } else {
            $valor_concatenado = $valor1;
        }
    } else {
        // Si uno de los valores no es numérico, no concatenar
        $valor_concatenado = $valor1;
    }
} else if($fecha1) {
    $valor_concatenado = $valor1;
} else {
    $valor_concatenado = 'No hay datos';
}

// Mostrar el valor concatenado y otras informaciones
$source = null;

// ============================ VER SI PASARON MENOS DE 10 SEGUNDOS =====================================
    // Crear objetos DateTime a partir de las fechas
    $dateTimeActual = new DateTime($fecha_actual);
    $dateTime1 = new DateTime($fecha1);

    // Calcular la diferencia
    $interval = $dateTimeActual->diff($dateTime1);

    // Calcular la diferencia total en segundos
    $tiempo_desde_ultimo_dato = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
// ============================ VER SI PASARON MENOS DE 10 SEGUNDOS =====================================

// CONSEGUIR IP 
$local_ip = gethostbyname(gethostname());

// Preparar la respuesta en formato JSON
header('Content-Type: application/json'); // Asegúrate de establecer el tipo de contenido

echo json_encode([
    "ip" => $local_ip,
    "valor1" => $valor1,
    "fecha1" => $fecha1,
    "valor2" => $valor2,
    "fecha2" => $fecha2,
    "diferencia_en_segundos" => $diferencia_en_segundos,
    "tiempo_desde_ultimo_dato" => $tiempo_desde_ultimo_dato,
    "valor_concatenado" => $valor_concatenado
]);
exit;
?>