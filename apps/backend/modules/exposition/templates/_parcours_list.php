<?php
$parcours = array();
foreach($exposition->getParcours() as $i){
  $parcours[] = $i->getLibelle();
}
echo join(', ', $parcours);