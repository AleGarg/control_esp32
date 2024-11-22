from comtypes import CLSCTX_ALL
from pycaw.pycaw import AudioUtilities, IAudioEndpointVolume

# Obtener la interfaz de control de audio
devices = AudioUtilities.GetSpeakers()
interface = devices.Activate(
    IAudioEndpointVolume._iid_, CLSCTX_ALL, None)
volume = interface.QueryInterface(IAudioEndpointVolume)

# Establecer el volumen al 50%
volume.SetMasterVolumeLevelScalar(0.5, None)

print("El volumen se ha establecido al 50%.")