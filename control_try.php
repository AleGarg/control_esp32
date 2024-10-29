<?php
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

    // VALORES POR SI LAS DUDAS:
    // 
    //     'CH-' : '45',    'CH'  : '46',   'CH+'      : '47',
    //     'PREV': '44',    'NEXT': '40',   'PLAYPAUSE': '43',
    //     'VOL-': '07',    'VOL+': '15',   'EQ'       : '09',
    //     '0'   : '16',    '100' : '19',   '200'      : '0d',
    //     '1'   : '0c',    '2'   : '18',   '3'        : '5e',
    //     '4'   : '08',    '5'   : '1c',   '6'        : '5a',
    //     '7'   : '42',    '8'   : '52',   '9'        : '4a'    

    if($data == 'PREV'){
        echo "<h1>
            Retrocediendo
        </h1>";
        system("cmd /c C:/control/ejecutar.bat");
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

// Obtener el último valor almacenado
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
        if ($diferencia_en_segundos < 10) {
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
echo "<br>Diferencia en segundos: $diferencia_en_segundos <br>";

if ($diferencia_en_segundos < 10) {
    echo "El valor es reciente<br>";
} else {
    echo "El valor es antiguo<br>";
}

// ============================ VER SI PASARON MENOS DE 10 SEGUNDOS =====================================
    $dateTimeActual = new DateTime($fecha_actual);
    $dateTime1 = new DateTime($fecha1);

    $interval = $dateTimeActual->diff($dateTime1);
    $tiempo_desde_ultimo_dato = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
    echo "Tiempo desde ultimo dato: " . $tiempo_desde_ultimo_dato."s<br>";   
// ============================ VER SI PASARON MENOS DE 10 SEGUNDOS =====================================

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="control.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <title>Control Remoto</title>
    <script src="script.js"></script>
</head>
<body>

    <?php
        $local_ip = gethostbyname(gethostname());
        echo "IP: ".$local_ip;
    ?>

    <div class="datos">
        <h2>Último dato recibido</h2>
        <p><?php echo $valor1 ?? 'No hay datos aún.'; ?></p>
        <p><?php if(isset($fecha1)){ echo date("H:i:s", strtotime($fecha1)); } ?></p>

        <h2>Anteúltimo dato recibido</h2>
        <p><?php echo $valor2 ?? 'No hay datos aún.'; ?></p>
        <p><?php if(isset($fecha2)){ echo date("H:i:s", strtotime($fecha2)); } ?></p>

        <h2>Diferencia de tiempo:</h2>
        <p><?php if(isset($diferencia_en_segundos)){ echo $diferencia_en_segundos; }?></p>
        <h2>Valores concatenados menos de 10s:</h2>
        <p><?php echo $valor_concatenado; ?></p>

    </div>
    <form method="post" action>
        <input type="text" name="data">
        <button type="submit" name="accion" value="Atender">ENVIAR DATA TIO</button>
    </form>

    <?php
        if(isset($valor_concatenado)){ // SI HAY DATO, SINO NO
            if ($valor_concatenado == '98') {
                echo "<h1>¡Combo activado!</h1>";
            }
            if($valor_concatenado == 'EQ'){
                echo "<h1>
                    SONANDO!!!!!!!!!!!!!!!!!!
                </h1>";
            }
            if($valor_concatenado == '1016'){
                echo "<h1>
                    Moviendo como loco!
                </h1>";
                system("cmd /c C:/control/ejecutar.bat");
            }
        }
    ?>

</body>
</html>
