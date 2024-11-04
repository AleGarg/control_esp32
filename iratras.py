from pynput.keyboard import Key, Controller
import time

keyboard = Controller()

time.sleep(0.5)  # Sleep for the amount of seconds generated

keyboard.press(Key.alt)
keyboard.press(Key.left)
keyboard.release(Key.left)
keyboard.release(Key.alt)