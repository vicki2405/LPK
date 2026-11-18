@echo off
title Memasang Masayume LMS Desktop...
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0Pasang-Shortcut.ps1"
if %errorlevel% neq 0 (
    echo.
    echo Gagal memasang shortcut secara otomatis.
    echo Silakan jalankan file Masayume.exe langsung di folder ini.
    pause
)
exit /b
