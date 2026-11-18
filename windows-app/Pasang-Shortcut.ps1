$ErrorActionPreference = "Stop"

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$sourceExe = Join-Path $scriptDir "Masayume.exe"
$sourceIco = Join-Path $scriptDir "app.ico"

# 1. Target Folder Instalasi Lokal
$installDir = Join-Path $env:LOCALAPPDATA "MasayumeLMS"
if (!(Test-Path $installDir)) {
    New-Item -ItemType Directory -Path $installDir -Force | Out-Null
}

$destExe = Join-Path $installDir "Masayume.exe"
$destIco = Join-Path $installDir "app.ico"

Copy-Item $sourceExe $destExe -Force
Copy-Item $sourceIco $destIco -Force

# 2. Buat Shortcut di Desktop
$wsh = New-Object -ComObject WScript.Shell
$desktopPath = [System.Environment]::GetFolderPath([System.Environment+SpecialFolder]::Desktop)
$desktopLnk = Join-Path $desktopPath "MASAYUME - LMS & CBT.lnk"

$shortcut = $wsh.CreateShortcut($desktopLnk)
$shortcut.TargetPath = $destExe
$shortcut.WorkingDirectory = $installDir
$shortcut.IconLocation = "$destIco, 0"
$shortcut.Description = "Aplikasi LMS & CBT Terpadu LPK Masayume (masayume.id)"
$shortcut.Save()

# 3. Buat Shortcut di Start Menu
$startMenuPath = [System.Environment]::GetFolderPath([System.Environment+SpecialFolder]::Programs)
$startMenuLnk = Join-Path $startMenuPath "MASAYUME - LMS & CBT.lnk"

$startShortcut = $wsh.CreateShortcut($startMenuLnk)
$startShortcut.TargetPath = $destExe
$startShortcut.WorkingDirectory = $installDir
$startShortcut.IconLocation = "$destIco, 0"
$startShortcut.Description = "Aplikasi LMS & CBT Terpadu LPK Masayume (masayume.id)"
$startShortcut.Save()

Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "🎉 BERHASIL MEMASANG APLIKASI MASAYUME DI PC WINDOWS!" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "1. Shortcut Desktop   : $desktopLnk" -ForegroundColor White
Write-Host "2. Shortcut Start Menu: $startMenuLnk" -ForegroundColor White
Write-Host "3. File Eksekusi      : $destExe" -ForegroundColor White
Write-Host "============================================================" -ForegroundColor Cyan

[System.Windows.Forms.MessageBox]::Show(
    "Aplikasi MASAYUME LMS & CBT berhasil dipasang di Desktop dan Start Menu PC Anda!`n`nSilakan klik ikon Masayume di Desktop untuk langsung membuka aplikasi.",
    "Instalasi Berhasil - Masayume LMS",
    [System.Windows.Forms.MessageBoxButtons]::OK,
    [System.Windows.Forms.MessageBoxIcon]::Information
) | Out-Null
