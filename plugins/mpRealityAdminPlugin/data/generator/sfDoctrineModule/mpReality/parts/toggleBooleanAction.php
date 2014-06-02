  public function executeToggleBoolean(sfWebRequest $request)
  {
	  $this->forward404Unless(
      Doctrine::getTable('<?php echo $this->getModelClass() ?>')->hasField($field = $request->getParameter('field'))
      && $record = Doctrine::getTable('<?php echo $this->getModelClass() ?>')->find($request->getParameter('pk'))
    );
	  
	  $record->set($field, !$record->get($field));
	  $record->save();
	  
	  $this->dispatcher->notify(new sfEvent($this, 'admin.controller.redirect'));

	  return $this->renderText($record->$field ? '1' : '0');	  
  }  