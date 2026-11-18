Add-Type -AssemblyName System.Drawing

$srcPath = "C:\Users\LENOVO LOQ\.gemini\antigravity\brain\70db0d26-2478-41d8-b1ad-761b1704997b\masayume_3d_icon_1794971126288.jpg"
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$destPng = Join-Path $scriptDir "app.png"
$destIco = Join-Path $scriptDir "app.ico"
$rawCopy = Join-Path $scriptDir "masayume-3d-icon.jpg"

Copy-Item $srcPath $rawCopy -Force

$src = [System.Drawing.Bitmap]::FromFile($srcPath)

# The squircle is centered in the 1024x1024 image from roughly (130, 130) with width/height ~764
$cropX = 135
$cropY = 135
$cropW = 754
$cropH = 754

$cropped = New-Object System.Drawing.Bitmap($cropW, $cropH, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
$cg = [System.Drawing.Graphics]::FromImage($cropped)
$cg.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
$cg.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic

$dRect1 = New-Object System.Drawing.Rectangle(0, 0, $cropW, $cropH)
$sRect1 = New-Object System.Drawing.Rectangle($cropX, $cropY, $cropW, $cropH)
$cg.DrawImage($src, $dRect1, $sRect1, [System.Drawing.GraphicsUnit]::Pixel)
$cg.Dispose()
$src.Dispose()

# Create 512x512 with smooth rounded squircle mask so the corners are transparent
$size = 512
$finalBmp = New-Object System.Drawing.Bitmap($size, $size, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
$fg = [System.Drawing.Graphics]::FromImage($finalBmp)
$fg.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
$fg.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$fg.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
$fg.Clear([System.Drawing.Color]::Transparent)

# Rounded rectangle path
$r = 95
$rw = $size - 8
$rh = $size - 8
$rect = New-Object System.Drawing.Rectangle(4, 4, $rw, $rh)
$path = New-Object System.Drawing.Drawing2D.GraphicsPath
$path.AddArc($rect.X, $rect.Y, $r, $r, 180, 90)
$path.AddArc($rect.Right - $r, $rect.Y, $r, $r, 270, 90)
$path.AddArc($rect.Right - $r, $rect.Bottom - $r, $r, $r, 0, 90)
$path.AddArc($rect.X, $rect.Bottom - $r, $r, $r, 90, 90)
$path.CloseFigure()

$fg.SetClip($path)
$dRect2 = New-Object System.Drawing.Rectangle(0, 0, $size, $size)
$sRect2 = New-Object System.Drawing.Rectangle(0, 0, $cropW, $cropH)
$fg.DrawImage($cropped, $dRect2, $sRect2, [System.Drawing.GraphicsUnit]::Pixel)
$fg.ResetClip()
$cropped.Dispose()

# Subtle outer glowing border
$pen = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(140, 56, 189, 248), 2.5)
$fg.DrawPath($pen, $path)
$pen.Dispose()
$fg.Dispose()

$finalBmp.Save($destPng, [System.Drawing.Imaging.ImageFormat]::Png)

# Generate multi-resolution ICO (256, 128, 64, 48, 32, 16)
$resolutions = @(256, 128, 64, 48, 32, 16)
$icoImages = @()

foreach ($res in $resolutions) {
    $thumb = New-Object System.Drawing.Bitmap($res, $res, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
    $tg = [System.Drawing.Graphics]::FromImage($thumb)
    $tg.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    $tg.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $tg.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    
    $dRectRes = New-Object System.Drawing.Rectangle(0, 0, $res, $res)
    $sRectRes = New-Object System.Drawing.Rectangle(0, 0, $size, $size)
    $tg.DrawImage($finalBmp, $dRectRes, $sRectRes, [System.Drawing.GraphicsUnit]::Pixel)
    $tg.Dispose()
    
    $ms = New-Object System.IO.MemoryStream
    $thumb.Save($ms, [System.Drawing.Imaging.ImageFormat]::Png)
    $pngData = $ms.ToArray()
    $ms.Dispose()
    $thumb.Dispose()
    
    $icoImages += [PSCustomObject]@{
        Width = $res
        Height = $res
        Data = $pngData
    }
}
$finalBmp.Dispose()

$fs = [System.IO.File]::Create($destIco)
$bw = New-Object System.IO.BinaryWriter($fs)
$bw.Write([uint16]0)
$bw.Write([uint16]1)
$bw.Write([uint16]$icoImages.Count)

$offset = 6 + (16 * $icoImages.Count)
foreach ($img in $icoImages) {
    $w = if ($img.Width -eq 256) { 0 } else { [byte]$img.Width }
    $h = if ($img.Height -eq 256) { 0 } else { [byte]$img.Height }
    $bw.Write([byte]$w)
    $bw.Write([byte]$h)
    $bw.Write([byte]0)
    $bw.Write([byte]0)
    $bw.Write([uint16]1)
    $bw.Write([uint16]32)
    $bw.Write([uint32]$img.Data.Length)
    $bw.Write([uint32]$offset)
    $offset += $img.Data.Length
}

foreach ($img in $icoImages) {
    $bw.Write($img.Data)
}

$bw.Flush()
$bw.Close()
$fs.Close()

Write-Host "✅ 3D Icon successfully processed and saved to app.png and app.ico!" -ForegroundColor Green
