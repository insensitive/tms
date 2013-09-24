<?php

namespace Truck\Form;

use Zend\Form\Form;
use AfcCommons\StaticOptions\StaticOptions;

class TruckForm extends Form {
	public function __construct($name = null) {
		$name = $name == null ? "frm_truck" : $name;
		
		// we want to ignore the name passed
		parent::__construct ( $name );
		$this->setAttribute ( 'method', 'post' );
		
		$this->setAttributes ( array (
				'method' => 'post',
				'class' => 'form-horizontal' 
		) );
		
		$this->add ( array (
				'name' => 'truck_id',
				'type' => 'hidden' 
		) );
		
		$this->add ( array (
				'name' => 'shipper_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getShippers () 
				)
				,
				'attributes' => array (
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'name',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Name',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'number',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Truck Number',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'make',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Make',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'color',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Color',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'status',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => array (
								'ACTIVE' => 'Active',
								'INACTIVE' => 'Inactive' 
						) 
				) 
		) );
		
		$this->add ( array (
				'name' => 'submit',
				'attributes' => array (
						'type' => 'submit',
						'value' => 'Save',
						'id' => 'submitTruckButton',
						'class' => "btn btn-primary btn-large" 
				) 
		) );
	}
	private function getShippers() {
		$em = StaticOptions::getEntityManager ();
		$qb = $em->createQueryBuilder ();
		$qb->select ( array (
				's.shipper_id',
				's.name' 
		) )->from ( 'Shipper\Entity\Shipper', 's' );
		$query = $qb->getQuery ();
		$shippers = $query->getArrayResult ();
		
		$options = array ();
		$options [""] = "-- Select Shipper --";
		foreach ( $shippers as $shipper ) {
			$options [$shipper ['shipper_id']] = $shipper ['name'];
		}
		return $options;
	}
}