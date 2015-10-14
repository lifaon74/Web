<?php

require_once('libraries/Compiler.class.php');

$ArduinoProMini = new ArduinoProMini($precompiler, 8000000);
$microcontroller = $ArduinoProMini->microcontroller;

//$compiler->compile('exemples/test.c.php', $microcontroller);

$compiler->compile('libraries/tools/Interrupt.h.php', $microcontroller);

?>