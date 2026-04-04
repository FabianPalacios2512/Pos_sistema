$fd = [char]0xFFFD
$file = 'c:\Users\Admin\Documents\105 pos\Pos_sistema\src\views\SaasRegister.vue'
$c = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::UTF8)

$a = [char]0xE1; $e = [char]0xE9; $i2 = [char]0xED; $o = [char]0xF3; $u2 = [char]0xFA
$n2 = [char]0xF1; $N2 = [char]0xD1
$bang = [char]0xA1; $quest = [char]0xBF
$U = [char]0xDA
$fd1 = [string]$fd; $fd2 = $fd1 + $fd1

Write-Host ("FFFD before: " + (($c -split [regex]::Escape($fd1)).Count - 1))

$fixes = @(
  @("Content Din" + $fd2 + "mico",       "Content Din" + $a + "mico"),
  @("Header Din" + $fd2 + "mico",        "Header Din" + $a + "mico"),
  @("L" + $fd2 + "nea conectora",        "L" + $i2 + "nea conectora"),
  @("L" + $fd2 + "nea",                  "L" + $i2 + "nea"),
  @("d" + $fd2 + "as",                   "d" + $i2 + "as"),
  @("actualizar" + $fd2 + " cuando",     "actualizar" + $a + " cuando"),
  @("actualizar" + $fd2,                 "actualizar" + $a),
  @("Pol" + $fd2 + "tica",               "Pol" + $i2 + "tica"),
  @("pol" + $fd2 + "tica",               "pol" + $i2 + "tica"),
  @("M" + $fd2 + "nimo 8",               "M" + $i2 + "nimo 8"),
  @("m" + $fd2 + "nimo",                 "m" + $i2 + "nimo"),
  @("T" + $fd2 + "tulo",                 "T" + $i2 + "tulo"),
  @("n" + $fd2 + "meros",                "n" + $u2 + "meros"),
  @("n" + $fd2 + "mero",                 "n" + $u2 + "mero"),
  @("N" + $fd2 + "mero",                 "N" + $u2 + "mero"),
  @("min" + $fd2 + "sculas",             "min" + $u2 + "sculas"),
  @("pesta" + $fd2 + "a",                "pesta" + $n2 + "a"),
  @("Mar" + $fd2 + "a",                  "Mar" + $i2 + "a"),
  @("enlace " + $fd2 + "nico",           "enlace " + $u2 + "nico"),
  @("Bot" + $fd1 + "n",                  "Bot" + $o + "n"),
  @("Gesti" + $fd1 + "n",                "Gesti" + $o + "n"),
  @("gesti" + $fd1 + "n",                "gesti" + $o + "n"),
  @("expansi" + $fd1 + "n",              "expansi" + $o + "n"),
  @("dise" + $fd1 + "ado",               "dise" + $n2 + "ado"),
  @("Optimizaci" + $fd1 + "n",           "Optimizaci" + $o + "n"),
  @("precisi" + $fd1 + "n",              "precisi" + $o + "n"),
  @("Jos" + $fd1 + " G",                 "Jos" + $e + " G"),
  @("Jos" + $fd1 + "<",                  "Jos" + $e + "<"),
  @("Patr" + $fd1 + "n",                 "Patr" + $o + "n"),
  @("m" + $fd1 + "vil",                  "m" + $o + "vil"),
  @("DISE" + $fd1 + "O",                 "DISE" + $N2 + "O"),
  @("reci" + $fd1 + "n",                 "reci" + $e + "n"),
  @("C" + $fd1 + "digo",                 "C" + $o + "digo"),
  @("Aceptaci" + $fd1 + "n",             "Aceptaci" + $o + "n"),
  @("facturaci" + $fd1 + "n",            "facturaci" + $o + "n"),
  @("informaci" + $fd1 + "n",            "informaci" + $o + "n"),
  @("Informaci" + $fd1 + "n",            "Informaci" + $o + "n"),
  @("soluci" + $fd1 + "n",               "soluci" + $o + "n"),
  @("Soluci" + $fd1 + "n",               "Soluci" + $o + "n"),
  @("operaci" + $fd1 + "n",              "operaci" + $o + "n"),
  @("integraci" + $fd1 + "n",            "integraci" + $o + "n"),
  @("versi" + $fd1 + "n",                "versi" + $o + "n"),
  @("sesi" + $fd1 + "n",                 "sesi" + $o + "n"),
  @("Sesi" + $fd1 + "n",                 "Sesi" + $o + "n"),
  @("generaci" + $fd1 + "n",             "generaci" + $o + "n"),
  @("configuraci" + $fd1 + "n",          "configuraci" + $o + "n"),
  @("recuperaci" + $fd1 + "n",           "recuperaci" + $o + "n"),
  @("validaci" + $fd1 + "n",             "validaci" + $o + "n"),
  @("actualizaci" + $fd1 + "n",          "actualizaci" + $o + "n"),
  @("creaci" + $fd1 + "n",               "creaci" + $o + "n"),
  @("eliminaci" + $fd1 + "n",            "eliminaci" + $o + "n"),
  @("selecci" + $fd1 + "n",              "selecci" + $o + "n"),
  @("notificaci" + $fd1 + "n",           "notificaci" + $o + "n"),
  @("autenticaci" + $fd1 + "n",          "autenticaci" + $o + "n"),
  @("autorizaci" + $fd1 + "n",           "autorizaci" + $o + "n"),
  @("conexi" + $fd1 + "n",               "conexi" + $o + "n"),
  @("direcci" + $fd1 + "n",              "direcci" + $o + "n"),
  @("Direcci" + $fd1 + "n",              "Direcci" + $o + "n"),
  @("condici" + $fd1 + "n",              "condici" + $o + "n"),
  @("opci" + $fd1 + "n",                 "opci" + $o + "n"),
  @("Opci" + $fd1 + "n",                 "Opci" + $o + "n"),
  @("descripci" + $fd1 + "n",            "descripci" + $o + "n"),
  @("comunicaci" + $fd1 + "n",           "comunicaci" + $o + "n"),
  @("transacci" + $fd1 + "n",            "transacci" + $o + "n"),
  @("publicaci" + $fd1 + "n",            "publicaci" + $o + "n"),
  @("colecci" + $fd1 + "n",              "colecci" + $o + "n"),
  @("administraci" + $fd1 + "n",         "administraci" + $o + "n"),
  @("verificaci" + $fd1 + "n",           "verificaci" + $o + "n"),
  @("separaci" + $fd1 + "n",             "separaci" + $o + "n"),
  @("ubicaci" + $fd1 + "n",              "ubicaci" + $o + "n"),
  @("visualizaci" + $fd1 + "n",          "visualizaci" + $o + "n"),
  @("paginaci" + $fd1 + "n",             "paginaci" + $o + "n"),
  @("implementaci" + $fd1 + "n",         "implementaci" + $o + "n"),
  @("acci" + $fd1 + "n",                 "acci" + $o + "n"),
  @("Acci" + $fd1 + "n",                 "Acci" + $o + "n"),
  @("Contrase" + $fd1 + "a",             "Contrase" + $n2 + "a"),
  @("enumeraci" + $fd1 + "n",            "enumeraci" + $o + "n"),
  @("C" + $fd1 + "dula",                 "C" + $e + "dula"),
  @("Electr" + $fd1 + "nico",            "Electr" + $o + "nico"),
  @("T" + $fd1 + "rminos",               "T" + $e + "rminos"),
  @("t" + $fd1 + "rminos",               "t" + $e + "rminos"),
  @("Atr" + $fd1 + "s",                  "Atr" + $a + "s"),
  @("est" + $fd1 + " ",                  "est" + $a + " "),
  @("est" + $fd1 + "n",                  "est" + $a + "n"),
  @($fd1 + "xito",                       $e + "xito"),
  @("m" + $fd1 + "dulo",                 "m" + $o + "dulo"),
  @("M" + $fd1 + "dulo",                 "M" + $o + "dulo"),
  @("tel" + $fd1 + "fono",               "tel" + $e + "fono"),
  @("a" + $fd1 + "n",                    "a" + $u2 + "n"),
  @("Si dice que S" + $fd1 + " ",        "Si dice que S" + $i2 + " "),
  @($fd1 + "Deseas",                     $quest + "Deseas"),
  @($fd1 + "Ya tienes",                  $quest + "Ya tienes"),
  @($fd1 + "Olvidaste",                  $bang + "Olvidaste"),
  @("cu" + $fd1 + "ntos",                "cu" + $a + "ntos"),
  @("compa" + $fd1,                      "compa" + $n2),
  @("da" + $fd1 + "o",                   "da" + $n2 + "o"),
  @("se" + $fd1 + "al",                  "se" + $n2 + "al")
)

$count = 0
foreach ($fix in $fixes) {
    $old = $fix[0]; $new = $fix[1]
    if ($c.Contains($old)) {
        $c = $c.Replace($old, $new)
        $count++
    }
}

$after = ($c -split [regex]::Escape($fd1)).Count - 1
Write-Host ("Fixes applied: " + $count)
Write-Host ("FFFD after: " + $after)

[System.IO.File]::WriteAllText($file, $c, [System.Text.Encoding]::UTF8)
Write-Host "Saved."
