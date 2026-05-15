<?php
session_start(); // Musíme nastartovat, abychom věděli, co čistíme
session_unset(); // Odstraní všechny proměnné ze session
session_destroy(); // Úplně zničí session

// Přesměrování na hlavní stránku po odhlášení
header("Location: index.php");
exit();
?>