<?php

namespace Trailer\Form;

use Zend\Form\Form;
use AfcCommons\StaticOptions\StaticOptions;

class TrailerForm extends Form {
	public function __construct($name = null) {
		$name = $name == null ? "frm_trailer" : $name;
		
		// we want to ignore the name passed
		parent::__construct ( $name );
		$this->setAttribute ( 'method', 'post' );
		
		$this->setAttributes ( array (
				'method' => 'post',
				'class' => 'form-horizontal' 
		) );
		$this->add ( array (
				'name' => 'trailer_id',
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
				'name' => 'plate_number',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Plate#',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'capacity',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Capacity',
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
						'id' => 'submitTrailerButton',
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