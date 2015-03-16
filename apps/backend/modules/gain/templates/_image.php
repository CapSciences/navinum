<?php
if($gain->getImage())
echo link_to('Voir', url_for('/gain/'.$gain->getImage()), array('target' => 'new'));
