<?php
class ApiCreateVisiteurMessage extends ApiBaseMessage
{
  public function __construct($body)
  {
    parent::__construct(sfConfig::get('app_create_visiteur_subject'), $body, 'text/html');
  }
}