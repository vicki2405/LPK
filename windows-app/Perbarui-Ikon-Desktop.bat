@echo off
title Memperbarui Ikon Desktop Masayume...
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0Update-Desktop-Icon.ps1"
echo.
echo Ikon 3D Masayume berhasil diperbarui!
echo Silakan cek Desktop Anda sekarang.
pause
exit /b
