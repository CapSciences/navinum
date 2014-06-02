<?php
$lang = array();
foreach($exposition_visiteur_needs->getLangue() as $p){
  $lang[] = $p->getLibelle();
}
echo join(', ', $lang);