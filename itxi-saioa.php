<?php
require_once 'bootstrap.php'; // Saioa hasi eta konfigurazioak kargatu
session_unset(); // Saio-aldagai guztiak ezabatu
session_destroy(); // Saioa suntsitu
header("Location: hasi-saioa.php"); // Saioa hasteko orrira birbideratu
exit;
?>
