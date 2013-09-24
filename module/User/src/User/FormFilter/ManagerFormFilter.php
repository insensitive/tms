<?php

namespace User\FormFilter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface as InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ManagerFormFilter implements InputFilterAwareInterface {
	protected $inputFilter;
	
	// Add content to these methods:
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	public function getInputFilter() {
		if (! $this->inputFilter) {
			
			// Configure Adapter
			$serviceLocator = \AfcCommons\StaticOptions\StaticOptions::getServiceLocator ();
			$dbAdapter = $serviceLocator->get ( 'Zend\Db\Adapter\Adapter' );
			
			$inputFilter = new InputFilter ();
			$factory = new InputFactory ();
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'firstname',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'lastname',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'telephone',
					'required' => false,
					'allow_empty' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'status',
					'allow_empty' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'password',
					'required' => true 
			) ) );
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'confirm_password',
					'required' => true,
					'validators' => array (
							array (
									'name' => 'identical',
									'options' => array (
											'token' => 'password' 
									) 
							) 
					) 
			) ) );
			
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
}