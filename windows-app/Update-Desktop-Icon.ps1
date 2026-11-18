$ErrorActionPreference = "SilentlyContinue"

Add-Type -TypeDefinition @"
using System;
using System.Runtime.InteropServices;
public class WinAPI {
    [DllImport("shell32.dll")]
    public static extern void SHChangeNotify(int wEventId, int uFlags, IntPtr dwItem1, IntPtr dwItem2);
}
"@

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$newIco = Join-Path $scriptDir "masayume_3d.ico"
$newExe = Join-Path $scriptDir "Masayume.exe"

# 1. Update ke folder program terinstall
$targetDirs = @(
    (Join-Path $env:LOCALAPPDATA "Programs\MasayumeLMS"),
    (Join-Path $env:LOCALAPPDATA "MasayumeLMS")
)

foreach ($dir in $targetDirs) {
    if (Test-Path $dir) {
        Copy-Item $newIco (Join-Path $dir "masayume_3d.ico") -Force
        Copy-Item $newIco (Join-Path $dir "app.ico") -Force
        Copy-Item $newExe (Join-Path $dir "Masayume.exe") -Force
    }
}

# 2. Hapus dan buat ulang shortcut Desktop
$desktopPaths = @(
    [System.Environment]::GetFolderPath([System.Environment+SpecialFolder]::Desktop),
    (Join-Path $env:USERPROFILE "Desktop"),
    (Join-Path $env:USERPROFILE "OneDrive\Desktop")
)

$wsh = New-Object -ComObject WScript.Shell
$activeInstallDir = Join-Path $env:LOCALAPPDATA "Programs\MasayumeLMS"
if (!(Test-Path $activeInstallDir)) {
    $activeInstallDir = Join-Path $env:LOCALAPPDATA "MasayumeLMS"
}
$activeExe = Join-Path $activeInstallDir "Masayume.exe"
$activeIco = Join-Path $activeInstallDir "masayume_3d.ico"

foreach ($dp in $desktopPaths) {
    if (Test-Path $dp) {
        $lnk = Join-Path $dp "MASAYUME - LMS & CBT.lnk"
        if (Test-Path $lnk) {
            Remove-Item $lnk -Force
        }
        $sc = $wsh.CreateShortcut($lnk)
        $sc.TargetPath = $activeExe
        $sc.WorkingDirectory = $activeInstallDir
        $sc.IconLocation = "$activeIco,0"
        $sc.Description = "Aplikasi LMS & CBT Terpadu LPK Masayume"
        $sc.Save()
    }
}

# 3. Flush Icon Cache Windows Explorer
[WinAPI]::SHChangeNotify(0x08000000, 0x0000, [IntPtr]::Zero, [IntPtr]::Zero)

Write-Host "Ikon 3D Masayume berhasil diperbarui di Desktop Anda!" -ForegroundColor Green
