$csc = 'C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe'
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path

$src = Join-Path $scriptDir "Program.cs"
$ico = Join-Path $scriptDir "app.ico"
$out = Join-Path $scriptDir "Masayume.exe"

$tempDir = Join-Path $env:TEMP "MasayumeBuild"
if (!(Test-Path $tempDir)) {
    New-Item -ItemType Directory -Path $tempDir -Force | Out-Null
}

$tempSrc = Join-Path $tempDir "Program.cs"
$tempIco = Join-Path $tempDir "app.ico"
$tempOut = Join-Path $tempDir "Masayume.exe"

Copy-Item $src $tempSrc -Force
Copy-Item $ico $tempIco -Force

Set-Location $tempDir

$cmd = "& `"$csc`" /target:winexe /optimize+ /platform:anycpu /r:System.dll /r:System.Windows.Forms.dll /r:System.Drawing.dll `"/win32icon:$tempIco`" `"/out:$tempOut`" `"$tempSrc`""
Invoke-Expression $cmd

if (Test-Path $tempOut) {
    Copy-Item $tempOut $out -Force
    Write-Host "✅ Sukses meng-compile: $out" -ForegroundColor Green
} else {
    Write-Host "❌ Gagal meng-compile Masayume.exe" -ForegroundColor Red
}
