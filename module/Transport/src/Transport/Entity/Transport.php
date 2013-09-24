<?php

namespace Transport\Entity;

use Doctrine\ORM\Mapping as ORM;
use \AfcCommons\Entity\Entity as Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="transport")
 */
class Transport extends Entity {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	public $transport_id;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $name;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	public $company_id;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	public $shipper_id;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	public $truck_id;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	public $trailer_id;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	public $driver_id;
	
	/**
	 * @ORM\Column(type="datetime");
	 */
	public $start_time;
	
	/**
	 * @ORM\Column(type="datetime");
	 */
	public $end_time;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $weight;
	
	/**
	 * @ORM\Column(type="integer");
	 */
	public $source_address_id;
	
	/**
	 * @ORM\Column(type="integer");
	 */
	public $destination_address_id;
	
	/**
	 * @ORM\Column(type="string", columnDefinition="ENUM('IN_PROGRESS', 'COMPLETED')")
	 */
	public $status;
	
	/**
	 * @ORM\Column(type="datetime");
	 */
	public $created_at;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	public $created_by;
	
	/**
	 * @ORM\Column(type="datetime");
	 */
	public $last_updated_at;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	public $last_updated_by;
	public function getGridSql(array $gridInitialData = array()) {
		// Get the where condition
		$where = $gridInitialData ["where"];
		
		// Get the count
		$count = $gridInitialData ["count"];
		
		// Get offset
		$offset = $gridInitialData ["offset"];
		
		// Get the order
		$order = $gridInitialData ["order"];
		
		$em = $this->getEntityManager ();
		
		$to = $count + $offset;
		if ($order != null) {
			$order = "ORDER BY " . $order;
		} else {
			if ($this->hasVariable ( 'created_at' )) {
				$order = "ORDER BY created_at DESC";
			}
		}
		
		$sql = "SELECT
        t.*,
        c.name as company_name,
        s.name as shipper_name,
        d.name as driver_name,
        tru.name as truck_name,
        tra.plate_number as plate_number,
        sa.description as source,
        da.description as destination
        FROM
        transport t
        JOIN company c ON t.company_id = c.company_id
        JOIN shipper s ON t.shipper_id = s.shipper_id 
        JOIN driver d ON t.driver_id = d.driver_id
        JOIN destination_address da ON da.destination_address_id = t.destination_address_id 
        JOIN source_address sa ON sa.source_address_id = t.source_address_id
        LEFT JOIN truck tru ON t.truck_id = tru.truck_id
        LEFT JOIN trailer tra ON t.trailer_id = tra.trailer_id
        WHERE {$where} {$order} LIMIT {$offset},{$to}";
		
		return $sql;
	}
	public function getFilteredRecordSql(array $gridInitialData = array()) {
		// Get the where condition
		$where = $gridInitialData ["where"];
		
		$sql = "SELECT
	    count(*) as total_records
	    FROM
	    transport
	    WHERE {$where}";
		
		return $sql;
	}
	public function getTotalRecordSql() {
		$sql = "SELECT
	    count(*) as total_records
	    FROM
	    transport";
		
		return $sql;
	}
	public function getShipper() {
		$em = $this->getEntityManager ()->getRepository ( 'Shipper\Entity\Shipper' );
		if ($this->shipper_id) {
			return $em->findOneBy ( array (
					'shipper_id' => $this->shipper_id 
			) );
		}
		return false;
	}
	public function getCompany() {
		$em = $this->getEntityManager ()->getRepository ( 'Company\Entity\Company' );
		if ($this->company_id) {
			return $em->findOneBy ( array (
					'company_id' => $this->company_id 
			) );
		}
		return false;
	}
	public function getDriver() {
		$em = $this->getEntityManager ()->getRepository ( 'Driver\Entity\Driver' );
		if ($this->driver_id) {
			return $em->findOneBy ( array (
					'driver_id' => $this->driver_id 
			) );
		}
		return false;
	}
	public function getTruck() {
		$em = $this->getEntityManager ()->getRepository ( 'Truck\Entity\Truck' );
		if ($this->truck_id) {
			return $em->findOneBy ( array (
					'truck_id' => $this->truck_id 
			) );
		}
		return false;
	}
	public function getTrailer() {
		$em = $this->getEntityManager ()->getRepository ( 'Trailer\Entity\Trailer' );
		if ($this->trailer_id) {
			return $em->findOneBy ( array (
					'trailer_id' => $this->trailer_id 
			) );
		}
		return false;
	}
	public function getSourceAddress() {
		$em = $this->getEntityManager ()->getRepository ( 'SourceAddress\Entity\SourceAddress' );
		if ($this->source_address_id) {
			return $em->findOneBy ( array (
					'source_address_id' => $this->source_address_id
			) );
		}
		return false;
	}
	public function getDestinationAddress() {
		$em = $this->getEntityManager ()->getRepository ( 'DestinationAddress\Entity\DestinationAddress' );
		if ($this->destination_address_id) {
			return $em->findOneBy ( array (
					'destination_address_id' => $this->destination_address_id
			) );
		}
		return false;
	}
}