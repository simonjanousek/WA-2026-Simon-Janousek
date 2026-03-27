<?php

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

