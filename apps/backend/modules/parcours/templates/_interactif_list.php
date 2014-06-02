<?php
$interactif = array();
foreach($parcours->getInteractif() as $i){
  $interactif[] = $i->getLibelle();
}
echo join(', ', $interactif);