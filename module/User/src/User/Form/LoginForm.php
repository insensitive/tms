<?php

namespace User\Form;

use Zend\Form\Form;

class LoginForm extends Form {
	public function __construct($name = null) {
		$name = $name == null ? "user_login" : $name;
		parent::__construct ( $name );
		$this->setAttribute ( 'method', 'post' );
		
		$this->add ( array (
		        'name' => 'email',
		        'attributes' => array (
		                'type' => 'email',
		                'placeholder' => 'Email Address',
		                'class' => 'input-block-level',
		                'required' => 'required'
		        ),
		        'options' => array (
		                'label' => false
		        )
		) );
		
		$this->add ( array (
				'name' => 'password',
				'attributes' => array (
						'type' => 'password',
						'placeholder' => 'Password',
						'class' => 'input-block-level',
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
						'value' => 'Sign in',
						'id' => 'loginButton',
						'class' => "btn btn-large btn-primary" 
				) 
		) );
	}
}