<?php
session_start();

$baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Pokud baseDir není jen samotné lomítko, ořízneme ho a přidáme ho ručně, 
// aby tam bylo vždycky právě jedno.
$baseDir = ($baseDir === '/') ? '/' : rtrim($baseDir, '/') . '/';

define('BASE_URL', $baseDir);

// TEST: Pokud ti web nefunguje, odkomentuj řádek níže, 
// obnov stránku a napiš mi, co to vypsalo:
// die(BASE_URL);


// ... zbytek souboru index.php zůstává stejný ...

//kompletní zobrazování chyb
ini_set('display_error', 1);
ini_set('display_startup_error', 1);
error_reporting(E_ALL);

// Dynamické zjištění základní adresy aplikace
// Vypočítá absolutní cestu ke složce, ve které běží tento index.php

//$baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
//define('BASE_URL', $baseDir);
//echo($baseDir);

//načtení třídy routeru pro zpracování ULR
require_once '../core/App.php';

//inicializace aplikace a spuštění procesu routování
$app = new App();


