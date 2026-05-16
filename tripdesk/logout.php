<?php
session_start(); // zahajit aby bylo co mazat
session_unset(); // odstraneni vsech promenych ze session
session_destroy(); // destrukce session

// přesměrování na hlavní stránku po odhlášení
header("Location: index.php");
exit();
?>