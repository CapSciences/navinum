<?php

/**
 * gain actions.
 *
 * @package    sf_sandbox
 * @subpackage gain
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autogainActions
 */
class gainActions extends autogainActions
{
    public function getIndexValidators()
    {
        $validators = parent::getIndexValidators();
        $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        return $validators;
    }
}
