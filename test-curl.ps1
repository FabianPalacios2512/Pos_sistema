$ErrorActionPreference = "Stop"

$SSH_KEY = "$env:USERPROFILE\.ssh\id_rsa"
$VPS = "root@72.61.73.245"
$SSH_OPTS = @("-i", $SSH_KEY, "-o", "StrictHostKeyChecking=no", "-o", "BatchMode=yes")

$REMOTE_CMD = @"
echo "--- PROBANDO R2 CON CURL ---"
curl -v "https://4c93dafd89a32c19dadf690f61dce2c9.r2.cloudflarestorage.com/105pos-media/test.txt"
echo "------------------------------"
"@

& ssh @SSH_OPTS $VPS $REMOTE_CMD
