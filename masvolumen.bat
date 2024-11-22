if not DEFINED IS_MINIMIZED set IS_MINIMIZED=1 && start "" /min "%~dpnx0" %* && exit
@echo off
@REM python "C:\control\messi.py" %*
python "C:\xampp\htdocs\control_esp32\masvolumen.py" %*
exit