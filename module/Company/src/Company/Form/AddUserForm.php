<?php

namespace User\Form;

use Zend\Form\Form;
use AfcCommons\Permissions\Acl\Acl;

class AddUserForm extends Form {
	public function __construct($name = null) {
		$name = $name == null ? "add_user" : $name;
		// we want to ignore the name passed
		parent::__construct ( $name );
		$this->setAttribute ( 'method', 'post' );
		$this->setAttributes ( array (
				'method' => 'post',
				'class' => 'form-horizontal' 
		) );
		$this->add ( array (
				'name' => 'user_id',
				'type' => 'hidden' 
		) );
		
		$this->add ( array (
				'name' => 'firstname',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* First Name',
						'class' => 'span4',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'lastname',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => 'Last Name',
						'class' => 'span4' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'password',
				'attributes' => array (
						'type' => 'password',
						'placeholder' => '* Password',
						'class' => 'span4',
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'confirm_password',
				'attributes' => array (
						'type' => 'password',
						'placeholder' => '* Confirm Password',
						'class' => 'span4',
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'afc_group_id',
				'type' => 'select',
				'attributes' => array (
						'class' => 'span4',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false,
						'value_options' => $this->getGroups () 
				) 
		) );
		
		$this->add ( array (
				'name' => 'email',
				'attributes' => array (
						'type' => 'email',
						'placeholder' => 'Email',
						'class' => 'span4' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'telephone',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => 'Contact Number',
						'class' => 'span4' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'description',
				'attributes' => array (
						'type' => 'textarea',
						'placeholder' => 'Description',
						'class' => 'span6',
						'id' => 'userDescription' 
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
						'id' => 'addUserButton',
						'class' => "btn btn-primary" 
				) 
		) );
	}
	private function getGroups() {
		$groups = array (
				'' => '< Select Role >' 
		);
		foreach ( Acl::$groups as $group_id => $group ) {
			if ($group != "GUEST") {
				$groups [$group_id] = ucwords ( strtolower ( $group ) );
			}
		}
		return $groups;
	}
}