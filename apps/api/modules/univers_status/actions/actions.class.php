<?php

/**
 * univers_status actions.
 *
 * @package    sf_sandbox
 * @subpackage univers_status
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autounivers_statusActions
 */
class univers_statusActions extends autounivers_statusActions
{
    public function getIndexValidators()
    {
        $validators = parent::getIndexValidators();
        $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        return $validators;
    }
}
