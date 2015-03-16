<?php
if($univers->getImage())
echo link_to('Voir', url_for('/univers/'.$univers->getImage()), array('target' => 'new'));
