<?php
$hm = array();
foreach($exposition_visiteur_needs->getPreferenceMedia() as $p){
  $hm[] = $p->getLibelle();
}
echo join(', ', $hm);