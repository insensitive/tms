<?php

namespace Company\Form;

use Zend\Form\Form;

class CompanyForm extends Form {
	public function __construct($name = null) {
		$name = $name == null ? "frm_company" : $name;
		
		// we want to ignore the name passed
		parent::__construct ( $name );
		$this->setAttribute ( 'method', 'post' );
		
		$this->setAttributes ( array (
				'method' => 'post',
				'class' => 'form-horizontal' 
		) );
		$this->add ( array (
				'name' => 'company_id',
				'type' => 'hidden' 
		) );
		
		$this->add ( array (
				'name' => 'name',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Name',
						'class' => 'input-block-level',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'address1',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Address 1',
						'class' => 'input-block-level',
						'required' => 'required'
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'address2',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => 'Address 2',
						'class' => 'input-block-level'
				),
				'options' => array (
						'label' => false
				)
		) );
		
		$this->add ( array (
				'name' => 'city',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* City',
						'class' => 'input-block-level',
						'required' => 'required'
				),
				'options' => array (
						'label' => false
				)
		) );
		
		$this->add ( array (
				'name' => 'provision',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Provision',
						'class' => 'input-block-level',
						'required' => 'required'
				),
				'options' => array (
						'label' => false
				)
		) );
		
		$this->add ( array (
				'name' => 'postal_code',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Postal Code',
						'class' => 'input-block-level',
						'required' => 'required'
				),
				'options' => array (
						'label' => false
				)
		) );
		
		$this->add ( array (
				'name' => 'phone_office',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => 'Office Contact Number',
						'class' => 'input-block-level',
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'phone_office_ext',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => 'Office Extension',
						'class' => 'input-block-level'
				)
		) );
		
		$this->add ( array (
				'name' => 'phone_cell',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => 'Cell Number',
						'class' => 'input-block-level'
				)
		) );
		
		$this->add ( array (
				'name' => 'email',
				'attributes' => array (
						'type' => 'email',
						'placeholder' => '* Email ID',
						'class' => 'input-block-level',
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'rate',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Rate',
						'class' => 'input-block-level',
						'required' => 'required'
				)
		) );
		
		$this->add ( array (
				'name' => 'status',
				'type' => 'select',
				'attributes' => array (
						'class' => 'input-block-level',
				),
				'options' => array (
						'label' => false,
						'value_options' => array(
							'ACTIVE' => 'Active',
							'INACTIVE' => 'Inactive',
						) 
				) 
		) );
		
		$this->add ( array (
				'name' => 'submit',
				'attributes' => array (
						'type' => 'submit',
						'value' => 'Save',
						'id' => 'submitCompanyButton',
						'class' => "btn btn-primary btn-large span2" 
				) 
		) );
	}
}