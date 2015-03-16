<?php
if($univers->getLogo())
echo link_to('Voir', url_for('/univers/'.$univers->getLogo()), array('target' => 'new'));
