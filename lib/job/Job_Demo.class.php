<?php

/**
 * Sample job
 * use it with : Resque::enqueue('default', 'Job_Demo', array());
 */
class Job_Demo
{
  public function perform()
  {
    sfContext::getInstance()->getMailer()->composeAndSend('demo@clever-age.com', 'wpottier@clever-age.com', "Test de job Resque", "coucou depuis le job resque");
  }
}