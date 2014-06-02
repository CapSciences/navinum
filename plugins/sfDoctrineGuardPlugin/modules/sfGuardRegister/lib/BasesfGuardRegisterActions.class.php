<?php

class BasesfGuardRegisterActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      $this->getUser()->setFlash('notice', 'You are already registered and signed in!');
      $this->redirect('@homepage');
    }

    $class = sfConfig::get('app_sf_guard_plugin_register_form', 'sfGuardRegisterForm');
    $this->form = new $class();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $event = new sfEvent($this, 'user.filter_register');
        $this->form = $this->dispatcher
          ->filter($event, $this->form)
          ->getReturnValue();

        $user = $this->form->save();
        $this->getUser()->signIn($user);

        $this->redirect('@homepage');
      }
    }
  }
}
