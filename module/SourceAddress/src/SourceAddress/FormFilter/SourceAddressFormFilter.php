<?php

namespace SourceAddress\FormFilter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface as InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class SourceAddressFormFilter implements InputFilterAwareInterface {
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
					'name' => 'description',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'contact',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'address',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'phone',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'cell',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
}