<?php
require 'inc/lib.php';
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_ok()) cerrar_sesion();
ir('login.php');
