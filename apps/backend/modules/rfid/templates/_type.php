<?php 
switch($rfid->getType())
{
	case "0": echo 'admin';break;
	case "1": echo 'animateur';break;
	case "2": echo 'visiteur';break;
    default: echo $rfid->getType();break;
}
?>