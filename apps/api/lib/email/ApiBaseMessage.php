<?php
class ApiBaseMessage extends Swift_Message
{
  public function __construct($subject, $body, $content_type = 'text/html')
  {
      /*
    $body .= <<<EOF
--

Email sent by Cap Sciences
EOF
    ;
      */
    parent::__construct($subject, $body, $content_type);

    // set all shared headers
    $this->setFrom(array(sfConfig::get('app_default_from') => sfConfig::get('app_default_from_name')));
  }
}