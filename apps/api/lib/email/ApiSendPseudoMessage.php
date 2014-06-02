<?php
class ApiSendPseudoMessage extends ApiBaseMessage
{
  public function __construct($body)
  {
    parent::__construct(sfConfig::get('app_send_pseudo_subject'), $body, 'text/html');
  }
}