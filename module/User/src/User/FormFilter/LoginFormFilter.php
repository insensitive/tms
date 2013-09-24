<?php
namespace User\FormFilter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface as InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class LoginFormFilter implements InputFilterAwareInterface{
    protected $inputFilter;
    
    // Add content to these methods:
    public function setInputFilter (InputFilterInterface $inputFilter)
    {
    	throw new \Exception("Not used");
    }
    
    public function getInputFilter ()
    {
    	if (! $this->inputFilter) {
    		$inputFilter = new InputFilter();
    		$factory = new InputFactory();
    
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'password',
    				'required' => true
    		)));
    
    		$this->inputFilter = $inputFilter;
    	}
    
    	return $this->inputFilter;
    }
}