from comtypes import CLSCTX_ALL
from pycaw.pycaw import AudioUtilities, IAudioEndpointVolume

# Obtener la interfaz de control de audio
devices = AudioUtilities.GetSpeakers()
interface = devices.Activate(
    IAudioEndpointVolume._iid_, CLSCTX_ALL, None)
volume = interface.QueryInterface(IAudioEndpointVolume)

# Obtener el volumen actual
current_volume = volume.GetMasterVolumeLevelScalar()  # Devuelve un valor entre 0.0 y 1.0

# Calcular el nuevo volumen (disminuir 10%)
new_volume = max(0.0, current_volume - 0.1)  # Asegurarse de no bajar de 0.0

# Establecer el nuevo volumen
volume.SetMasterVolumeLevelScalar(new_volume, None)

print(f"El volumen se ha reducido un 10%. Nuevo volumen: {new_volume * 100:.0f}%")