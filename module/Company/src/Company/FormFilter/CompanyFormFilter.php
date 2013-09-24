<?php

namespace Company\FormFilter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface as InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CompanyFormFilter implements InputFilterAwareInterface {
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
					'name' => 'name',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'address1',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'address2',
					'required' => false,
					'allowEmpty' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'city',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'provision',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'postal_code',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'phone_office',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'phone_office_ext',
					'required' => false,
					'allowEmpty' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'phone_cell',
					'required' => false,
					'allowEmpty' => true,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'email',
					'required' => true,
					'allowEmpty' => false,
					'filters' => array (
							array (
									'name' => 'StringTrim' 
							) 
					),
					'validators' => array (
							array (
									'name' => 'EmailAddress' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'rate',
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