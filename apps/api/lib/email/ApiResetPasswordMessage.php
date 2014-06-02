<?php
class ApiResetPasswordMessage extends ApiBaseMessage
{
  public function __construct($body)
  {
    parent::__construct(sfConfig::get('app_reset_password_subject'), $body, 'text/html');
  }
}