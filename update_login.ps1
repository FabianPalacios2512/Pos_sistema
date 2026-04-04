$filePath = "src\components\LoginView.vue"
$content = Get-Content -Path $filePath -Raw
$newTemplate = @"
"@
$pattern = "(?s)<template>.*?</template>"
$newContent = [System.Text.RegularExpressions.Regex]::Replace($content, $pattern, $newTemplate)
Set-Content -Path $filePath -Value $newContent -Encoding UTF8
