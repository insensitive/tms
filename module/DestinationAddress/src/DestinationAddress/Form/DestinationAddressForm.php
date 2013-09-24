<?php

namespace DestinationAddress\Form;

use Zend\Form\Form;
use AfcCommons\StaticOptions\StaticOptions;

class DestinationAddressForm extends Form {
	public function __construct($name = null) {
		$name = $name == null ? "frm_destination_address" : $name;
		
		// we want to ignore the name passed
		parent::__construct ( $name );
		$this->setAttribute ( 'method', 'post' );
		
		$this->setAttributes ( array (
				'method' => 'post',
				'class' => 'form-horizontal' 
		) );
		
		$this->add ( array (
				'name' => 'destination_address_id',
				'type' => 'hidden' 
		) );
		
		$this->add ( array (
				'name' => 'company_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getCompanies () 
				),
				'attributes' => array (
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'description',
				'attributes' => array (
						'type' => 'textarea',
						'placeholder' => '* Description',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'contact',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Destination Address Contact',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'address',
				'attributes' => array (
						'type' => 'textarea',
						'placeholder' => '* Address',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'phone',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Phone',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'cell',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Cell',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'licence',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Diversion Licence',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'depth',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Depth of formation',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'submit',
				'attributes' => array (
						'type' => 'submit',
						'value' => 'Save',
						'id' => 'submitDestinationAddressButton',
						'class' => "btn btn-primary btn-large" 
				) 
		) );
	}
	private function getCompanies() {
		$em = StaticOptions::getEntityManager ();
		$qb = $em->createQueryBuilder ();
		$qb->select ( array (
				'c.company_id',
				'c.name' 
		) )->from ( 'Company\Entity\Company', 'c' );
		$query = $qb->getQuery ();
		$companies = $query->getArrayResult ();
		
		$options = array ();
		$options [""] = "-- Select Customer --";
		foreach ( $companies as $company ) {
			$options [$company ['company_id']] = $company ['name'];
		}
		return $options;
	}
}