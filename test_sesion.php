<?php
session_start();
$_SESSION['prueba'] = 'Funciona la sesión';
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
