<?php

namespace Transport\Form;

use Zend\Form\Form;
use AfcCommons\StaticOptions\StaticOptions;

class TransportForm extends Form {
	private $is_edit;
	public function __construct($name = null, $is_edit = false) {
		$this->is_edit = $is_edit;
		$name = $name == null ? "frm_transport" : $name;
		
		// we want to ignore the name passed
		parent::__construct ( $name );
		$this->setAttribute ( 'method', 'post' );
		
		$this->setAttributes ( array (
				'method' => 'post',
				'class' => 'form-horizontal' 
		) );
		
		$this->add ( array (
				'name' => 'transport_id',
				'type' => 'hidden' 
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
				'name' => 'company_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getCompanies () 
				),
				'attributes' => array (
						'required' => 'required',
						'id' => 'company_id'
				) 
		) );
		
		$this->add ( array (
				'name' => 'truck_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getFreeTrucks () 
				),
				'attributes' => array (
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'trailer_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getFreeTrailer () 
				),
				'attributes' => array (
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'driver_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getFreeDrivers () 
				),
				'attributes' => array (
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'start_time',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Start Time',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'weight',
				'attributes' => array (
						'type' => 'text',
						'placeholder' => '* Weight',
						'required' => 'required' 
				),
				'options' => array (
						'label' => false 
				) 
		) );
		
		$this->add ( array (
				'name' => 'source_address_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getSourceAddresses ()
				),
				'attributes' => array (
						'required' => 'required'
				)
		) );
		
		$this->add ( array (
				'name' => 'destination_address_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getDestinationAddresses ()
				),
				'attributes' => array (
						'required' => 'required'
				)
		) );
		
		
		$this->add ( array (
				'name' => 'submit',
				'attributes' => array (
						'type' => 'submit',
						'value' => 'Save',
						'id' => 'submitTransportButton',
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
		$options [""] = "-- Select Company --";
		foreach ( $companies as $company ) {
			$options [$company ['company_id']] = $company ['name'];
		}
		return $options;
	}
	private function getFreeTrucks() {
		$conn = StaticOptions::getEntityManager ()->getConnection ();
		
		$where = " 1=1 ";
		if (! $this->is_edit) {
			$where = " t.truck_id NOT IN (SELECT DISTINCT(truck_id) FROM transport WHERE status='IN_PROGRESS') ";
		}
		$query = "SELECT t.truck_id,t.name FROM truck t WHERE {$where}";
		
		$results = $conn->executeQuery ( $query );
		$results = $results->fetchAll ();
		
		$options = array ();
		$options ["0"] = "-- Select Truck --";
		foreach ( $results as $truck ) {
			$options [$truck ['truck_id']] = $truck ['name'];
		}
		return $options;
	}
	private function getFreeTrailer() {
		$conn = StaticOptions::getEntityManager ()->getConnection ();
		
		$where = " 1=1 ";
		if (! $this->is_edit) {
			$where = " t.trailer_id NOT IN (SELECT DISTINCT(trailer_id) FROM transport WHERE status='IN_PROGRESS') ";
		}
		$query = "SELECT t.trailer_id,t.plate_number FROM trailer t WHERE {$where}";
		
		$results = $conn->executeQuery ( $query );
		$results = $results->fetchAll ();
		
		$options = array ();
		$options ["0"] = "-- Select Trailer --";
		foreach ( $results as $trailer ) {
			$options [$trailer ['trailer_id']] = $trailer ['plate_number'];
		}
		return $options;
	}
	private function getFreeDrivers() {
		$conn = StaticOptions::getEntityManager ()->getConnection ();
		$where = " 1=1 ";
		if (! $this->is_edit) {
			$where = " d.driver_id NOT IN (SELECT DISTINCT(driver_id) FROM transport WHERE status='IN_PROGRESS') ";
		}
		$query = "SELECT d.driver_id,d.name FROM driver d WHERE {$where}";
		
		$results = $conn->executeQuery ( $query );
		$results = $results->fetchAll ();
		
		$options = array ();
		$options [""] = "-- Select Driver --";
		foreach ( $results as $driver ) {
			$options [$driver ["driver_id"]] = $driver ["name"];
		}
		return $options;
	}
	private function getSourceAddresses(){
		$conn = StaticOptions::getEntityManager ()->getConnection ();
		$query = "SELECT sa.description,sa.source_address_id FROM source_address sa";
		
		$results = $conn->executeQuery ( $query );
		$results = $results->fetchAll ();
		
		$options = array ();
		$options [""] = "-- Select Source Address --";
		foreach ( $results as $source_address ) {
			$options [$source_address ["source_address_id"]] = $source_address ["description"];
		}
		return $options;
	}
	private function getDestinationAddresses(){
		$conn = StaticOptions::getEntityManager ()->getConnection ();
		$query = "SELECT sa.description,sa.destination_address_id FROM destination_address sa";
	
		$results = $conn->executeQuery ( $query );
		$results = $results->fetchAll ();
	
		$options = array ();
		$options [""] = "-- Select Destination Address --";
		foreach ( $results as $source_address ) {
			$options [$source_address ["destination_address_id"]] = $source_address ["description"];
		}
		return $options;
	}
}