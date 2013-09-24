<?php 
namespace User\Form;

use Zend\Form\Form;

class RegistrationForm extends Form
{
    public function __construct ($name = null)
    {
        $name = $name == null ? "user_registration" : $name;
        // we want to ignore the name passed
        parent::__construct($name);
        $this->setAttribute('method', 'post');
    
        $this->add(array(
                'name' => 'user_id',
                'type' => 'hidden'
        ));
    
        $this->add(array(
                'name' => 'username',
                'attributes' => array(
                        'type' => 'text',
                        'placeholder' => '* Username',
                        'class' => 'span12',
                        'required' => 'required'
                ),
                'options' => array(
                        'label' => false
                )
        ));
        
        $this->add(array(
                'name' => 'password',
                'attributes' => array(
                        'type' => 'password',
                        'placeholder' => '* Password',
                        'class' => 'span12',
                        'required' => 'required'
                ),
                'options' => array(
                        'label' => "Password"
                )
        ));
        
        $this->add(array(
                'name' => 'confirm_password',
                'attributes' => array(
                        'type' => 'password',
                        'placeholder' => '* Confirm Password',
                        'class' => 'span12',
                        'required' => 'required'
                ),
                'options' => array(
                        'label' => "Confirm Password"
                )
        ));
    
        $this->add(array(
                'name' => 'role',
                'type' => 'select',
                'attributes' => array(
                        'class' => 'span12',
                        'required' => 'required'
                ),
                'options' => array(
                        'label' => false,
                        'value_options' => array(
                                '' => 'Select Role',
                                'ADMINISTRATOR' => 'administrator',
                                'MANAGER' => 'manager',
                                'CASHIER' => 'cashier',
                        )
                ),
        ));
        
        $this->add(array(
                'name' => 'email',
                'attributes' => array(
                        'type' => 'email',
                        'placeholder' => '* Email',
                        'class' => 'span12',
                        'required' => 'required'
                ),
                'options' => array(
                        'label' => false
                )
        ));
        
        $this->add(array(
                'name' => 'telephone',
                'attributes' => array(
                        'type' => 'text',
                        'placeholder' => '* Contact Number',
                        'class' => 'span6',
                        'required' => 'required'
                ),
                'options' => array(
                        'label' => false
                )
        ));
        
        $this->add(array(
                'name' => 'description',
                'attributes' => array(
                        'type' => 'text',
                        'placeholder' => '* Description',
                        'class' => 'span12',
                        'required' => 'required'
                ),
                'options' => array(
                        'label' => false
                )
        ));
        
        $this->add(array(
                'name' => 'submit',
                'attributes' => array(
                        'type' => 'submit',
                        'value' => 'Register',
                        'id' => 'registerButton',
                        'class' => "btn"
                )
        ));
    }
}