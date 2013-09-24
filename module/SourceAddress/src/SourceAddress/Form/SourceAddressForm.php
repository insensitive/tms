<?php

namespace SourceAddress\Form;

use Zend\Form\Form;

class SourceAddressForm extends Form {
	public function __construct($name = null) {
		$name = $name == null ? "frm_source_address" : $name;
		
		// we want to ignore the name passed
		parent::__construct ( $name );
		$this->setAttribute ( 'method', 'post' );
		
		$this->setAttributes ( array (
				'method' => 'post',
				'class' => 'form-horizontal' 
		) );
		
		$this->add ( array (
				'name' => 'source_address_id',
				'type' => 'hidden' 
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
						'placeholder' => '* Source Address Contact',
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
						'type' => 'textarea',
						'placeholder' => '* Cell',
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
						'id' => 'submitSourceAddressButton',
						'class' => "btn btn-primary btn-large" 
				) 
		) );
	}
}