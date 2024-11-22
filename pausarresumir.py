from pynput.keyboard import Key, Controller
import time

keyboard = Controller()

time.sleep(0.5)

keyboard.press(Key.media_play_pause)
keyboard.release(Key.media_play_pause)