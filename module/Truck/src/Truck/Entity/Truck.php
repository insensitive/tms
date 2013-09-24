<?php

namespace Truck\Entity;

use Doctrine\ORM\Mapping as ORM;
use \AfcCommons\Entity\Entity as Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="truck")
 */
class Truck extends Entity {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $truck_id;
    
    /**
    * @ORM\Column(type="integer")
    */
    public $shipper_id;
    
    /**
     * @ORM\Column(type="string");
     */
    public $name;
    
    /**
     * @ORM\Column(type="string");
     */
    public $number;
    
    /**
     * @ORM\Column(type="string");
     */
    public $make;
    
    /**
     * @ORM\Column(type="string");
     */
    public $color;
    
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('ACTIVE', 'INACTIVE')")
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
	s.name as shipper_name
        FROM
        truck t,
	shipper s
        WHERE t.shipper_id = s.shipper_id AND {$where} {$order} LIMIT {$offset},{$to}";
        
        return $sql;
    }
    
    public function getFilteredRecordSql(array $gridInitialData = array()) {
        // Get the where condition
        $where = $gridInitialData ["where"];
        
        $sql = "SELECT
	    count(*) as total_records
	    FROM
	    truck t
	    WHERE {$where}";
        
        return $sql;
    }
    
    public function getTotalRecordSql() {
        $sql = "SELECT
	    count(*) as total_records
	    FROM
	    truck";
        
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
}