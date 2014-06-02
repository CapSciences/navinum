<?php
$permissions = array();
foreach($sf_guard_user->getPermissions() as $i){
  $permissions[] = $i->getName();
}
echo join(', ', $permissions);