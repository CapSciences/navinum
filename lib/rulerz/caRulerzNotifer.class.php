<?php

class caRulerzNotifier extends Doctrine_Template
{
    public function setTableDefinition()
    {

        if(isset($GLOBALS['argv']) && count($GLOBALS['argv']) && strpos($GLOBALS['argv'][0], 'symfony') !== false){
		// do nothing : come from cli
		//die('do nothing');
	}else{
        	$this->addListener(new caRulerzEntityListener($this->_options));
	}
    }
}
