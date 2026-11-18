$ErrorActionPreference = "Stop"

$csc = 'C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe'
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path

$tempDir = Join-Path $env:TEMP "MasayumeInstallerBuild"
if (!(Test-Path $tempDir)) {
    New-Item -ItemType Directory -Path $tempDir -Force | Out-Null
}

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "🔨 MEMBANGUN WINDOWS SETUP INSTALLER: Masayume-LMS-Setup.exe" -ForegroundColor Green
Write-Host "==========================================================" -ForegroundColor Cyan

# 1. Salin sumber berkas ke temp
$srcApp = Join-Path $scriptDir "Program.cs"
$srcUninst = Join-Path $scriptDir "Uninstall.cs"
$srcSetup = Join-Path $scriptDir "SetupWizard.cs"
$ico = Join-Path $scriptDir "masayume_3d.ico"

$tempApp = Join-Path $tempDir "Program.cs"
$tempUninst = Join-Path $tempDir "Uninstall.cs"
$tempSetup = Join-Path $tempDir "SetupWizard.cs"
$tempIco = Join-Path $tempDir "masayume_3d.ico"

Copy-Item $srcApp $tempApp -Force
Copy-Item $srcUninst $tempUninst -Force
Copy-Item $srcSetup $tempSetup -Force
Copy-Item $ico $tempIco -Force

Set-Location $tempDir

# 2. Compile Masayume.exe
Write-Host "1. Meng-compile Masayume.exe..." -ForegroundColor Yellow
$tempExe = Join-Path $tempDir "Masayume.exe"
$cmdApp = "& `"$csc`" /target:winexe /optimize+ /platform:anycpu /r:System.dll /r:System.Windows.Forms.dll /r:System.Drawing.dll `"/win32icon:$tempIco`" `"/out:$tempExe`" `"$tempApp`""
Invoke-Expression $cmdApp

if (!(Test-Path $tempExe)) {
    throw "Gagal compile Masayume.exe"
}

# 3. Compile uninstall.exe
Write-Host "2. Meng-compile uninstall.exe..." -ForegroundColor Yellow
$tempUninstExe = Join-Path $tempDir "uninstall.exe"
$cmdUninst = "& `"$csc`" /target:winexe /optimize+ /platform:anycpu /r:System.dll /r:System.Windows.Forms.dll `"/win32icon:$tempIco`" `"/out:$tempUninstExe`" `"$tempUninst`""
Invoke-Expression $cmdUninst

if (!(Test-Path $tempUninstExe)) {
    throw "Gagal compile uninstall.exe"
}

# 4. Compile Masayume-LMS-Setup.exe dengan Embedded Payload
Write-Host "3. Merakit Setup Wizard Installer dengan Embedded Payload..." -ForegroundColor Yellow
$tempSetupExe = Join-Path $tempDir "Masayume-LMS-Setup.exe"

$resApp = "/resource:$tempExe,Masayume.exe"
$resUninst = "/resource:$tempUninstExe,uninstall.exe"
$resIco = "/resource:$tempIco,masayume_3d.ico"

$cmdSetup = "& `"$csc`" /target:winexe /optimize+ /platform:anycpu /r:System.dll /r:System.Windows.Forms.dll /r:System.Drawing.dll `"/win32icon:$tempIco`" `"$resApp`" `"$resUninst`" `"$resIco`" `"/out:$tempSetupExe`" `"$tempSetup`""
Invoke-Expression $cmdSetup

if (!(Test-Path $tempSetupExe)) {
    throw "Gagal compile Masayume-LMS-Setup.exe"
}

# 5. Salin hasil akhir ke folder windows-app
$finalSetup = Join-Path $scriptDir "Masayume-LMS-Setup.exe"
Copy-Item $tempSetupExe $finalSetup -Force
Copy-Item $tempExe (Join-Path $scriptDir "Masayume.exe") -Force

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "🎉 SUKSES! INSTALLER WINDOWS BERHASIL DIBUAT:" -ForegroundColor Green
Write-Host "📁 $finalSetup" -ForegroundColor White
Write-Host "==========================================================" -ForegroundColor Cyan
