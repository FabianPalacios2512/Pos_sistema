$ErrorActionPreference = "Stop"

$cacertUrl = "https://curl.se/ca/cacert.pem"
$tempPath = Join-Path $Pshome "temp_cacert.pem"

Write-Host "Downloading latest cacert.pem from $cacertUrl to $tempPath..."

try {
    Invoke-WebRequest -Uri $cacertUrl -OutFile $tempPath
    Write-Host "Download successful."
} catch {
    Write-Error "Failed to download cacert.pem: $($_.Exception.Message)"
    exit 1
}

$destinationPath = "C:\Users\Fabian Paternina\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\cacert.pem"

Write-Host "Attempting to replace existing cacert.pem at $destinationPath..."

try {
    # Take ownership and grant full control to the current user (if needed)
    # This part might require elevated privileges. 
    # For simplicity, we'll try to replace directly first.

    if (Test-Path $destinationPath) {
        Remove-Item $destinationPath -Force
        Write-Host "Existing cacert.pem removed."
    }

    Move-Item $tempPath $destinationPath -Force
    Write-Host "cacert.pem updated successfully."
} catch {
    Write-Error "Failed to replace cacert.pem: $($_.Exception.Message)"
    Write-Host "Please ensure you have sufficient permissions to modify '$destinationPath'. You might need to run PowerShell as Administrator."
    exit 1
}