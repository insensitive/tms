<?php

namespace Shipper\Entity;

use Doctrine\ORM\Mapping as ORM;
use \AfcCommons\Entity\Entity as Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="shipper")
 */
class Shipper extends Entity {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $shipper_id;
    
    /**
     * @ORM\Column(type="string");
     */
    public $name;
    
    /**
     * @ORM\Column(type="string");
     */
    public $address1;
    
    /**
     * @ORM\Column(type="string");
     */
    public $address2;
    /**
     * @ORM\Column(type="string");
     */
    public $city;
    /**
     * @ORM\Column(type="string");
     */
    public $provision;
    
    /**
     * @ORM\Column(type="string");
     */
    public $postal_code;
    
    /**
     * @ORM\Column(type="string");
     */
    public $phone_office;
    /**
     * @ORM\Column(type="string");
     */
    public $phone_office_ext;
    /**
     * @ORM\Column(type="string");
     */
    public $phone_cell;
    
    /**
     * @ORM\Column(type="string");
     */
    public $email;
    
    /**
     * @ORM\Column(type="decimal", precision=4, scale=1)
     */
    public $rate;
    
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
        s.*
        FROM
        shipper s
        WHERE {$where} {$order} LIMIT {$offset},{$to}";
        
        return $sql;
    }
    
    public function getFilteredRecordSql(array $gridInitialData = array()) {
        // Get the where condition
        $where = $gridInitialData ["where"];
        
        $sql = "SELECT
	    count(*) as total_records
	    FROM
	    shipper
	    WHERE {$where}";
        
        return $sql;
    }
    
    public function getTotalRecordSql() {
        $sql = "SELECT
	    count(*) as total_records
	    FROM
	    shipper";
        
        return $sql;
    }
}