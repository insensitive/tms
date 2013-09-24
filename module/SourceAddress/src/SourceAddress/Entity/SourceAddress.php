<?php

namespace SourceAddress\Entity;

use Doctrine\ORM\Mapping as ORM;
use \AfcCommons\Entity\Entity as Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="source_address")
 */
class SourceAddress extends Entity {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	public $source_address_id;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $description;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $contact;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $address;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $phone;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $cell;
	
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
        t.*
        FROM
        source_address t
        WHERE {$where} {$order} LIMIT {$offset},{$to}";
		
		return $sql;
	}
	public function getFilteredRecordSql(array $gridInitialData = array()) {
		// Get the where condition
		$where = $gridInitialData ["where"];
		
		$sql = "SELECT
	    count(*) as total_records
	    FROM
	    source_address t
	    WHERE {$where}";
		
		return $sql;
	}
	public function getTotalRecordSql() {
		$sql = "SELECT
	    count(*) as total_records
	    FROM
	    source_address";
		
		return $sql;
	}
}