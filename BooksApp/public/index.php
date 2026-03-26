<?php

//kompletní zobrazování chyb
ini_set('display_error', 1);
ini_set('display_startup_error', 1);
error_reporting(E_ALL);

//načtení třídy routeru pro zpracování ULR
require_once '../core/App.php';

//inicializace aplikace a spuštění procesu routování
$app = new App();

