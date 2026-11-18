@echo off
title Membuka Masayume LMS ^& CBT...
cd /d "%~dp0"
if exist "%~dp0Masayume.exe" (
    start "" "%~dp0Masayume.exe"
    exit /b
)

:: Fallback jika .exe belum ada
start msedge.exe --app="https://masayume.id" --user-data-dir="%LOCALAPPDATA%\MasayumeLMS\Profile" --start-maximized
exit /b
