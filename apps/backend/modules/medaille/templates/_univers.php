<?php
$arr_univers = array();
foreach($medaille->getUnivers() as $univers){
    $arr_univers[] = $univers->getLibelle();
}
echo implode(', ', $arr_univers);