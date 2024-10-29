import time
import network
import usocket as socket
from machine import Pin
from ir_rx import NEC_16

# Configurar la conexión Wi-Fi
ssid = 'Estudiantes'
password = 'educar_2018'

station = network.WLAN(network.STA_IF)
station.active(True)
station.connect(ssid, password)

while not station.isconnected():
    pass

print('Conexión establecida:', station.ifconfig())

# Variables para almacenar el valor recibido
last_data_hex = None
valor_recibido = None


# LED
led = Pin(2, Pin.OUT)
ledState = False

# =========================== VARIABLES ====================================
# Definición de variables del control remoto
control_remoto = {
    'CH-':  '45', 'CH':   '46', 'CH_mas':        '47',
    'PREV': '44', 'NEXT': '40', 'PLAYPAUSE':  '43',
    'VOL-': '07', 'VOL_mas': '15', 'EQ':         '09',
    '0':    '16', '100':  '19', '200':        '0d',
    '1':    '0c', '2':    '18', '3':          '5e',
    '4':    '08', '5':    '1c', '6':          '5a',
    '7':    '42', '8':    '52', '9':          '4a'
}


def callback(data, addr, ctrl):
    global last_data_hex
    if data > 0:
        last_data_hex = "{:02x}".format(data)
        print(f'Nuevo dato recibido: {last_data_hex}')
        
        # COMPROBAR SU VALOR
        
        if last_data_hex in control_remoto.values():
            for key, value in control_remoto.items():
                if value == last_data_hex:
                    valor_recibido = key
                    print(f'El valor recibido es: {valor_recibido}')  # Muestra el texto correspondiente
        
        
        
        
        
        global ledState  # Declara ledState como global
        if last_data_hex == '9':  # Usando '9' directamente
            ledState = not ledState  # Cambia el estado del LED
            led.value(ledState)  # Enciende o apaga el LED
            print('LED encendido' if ledState else 'LED apagado')
            
        enviar_datos(valor_recibido, ledState)  # Llama a la función para enviar datos


def enviar_datos(valor, ledvalor):
    server_ip = '10.0.13.114'  # IP de tu computadora (VER EN IPCONFIG)
    server_port = 80  # Puerto donde está escuchando tu servidor (CREO QUE NO ES NECESARIO)

    try:
        s = socket.socket()
        s.connect((server_ip, server_port))
        
        # Preparar la solicitud POST
        request = f"POST /control_esp32/control_esp32.php HTTP/1.1\r\n"
        request += f"Host: {server_ip}\r\n"
        request += "Content-Type: application/x-www-form-urlencoded\r\n"
        request += f"Content-Length: {len('data=' + valor)}\r\n"
        request += "Connection: close\r\n\r\n"
        request += f"data={valor}"  # Datos en el cuerpo de la solicitud
        
        # Enviar la solicitud
        s.send(request.encode())
        response = s.recv(1024)  # Puedes recibir la respuesta del servidor si lo necesitas
        print('Respuesta del servidor:', response.decode())
        
    except Exception as e:
        print("Error al enviar datos:", e)
    finally:
        s.close()

# Configurar el receptor IR
ir = NEC_16(Pin(26, Pin.IN), callback)

# Mantener el script corriendo
while True:
    time.sleep(0.1)