<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends \AfcCommons\Entity\User
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $user_id;

    /**
     * @ORM\Column(type="string");
     */
    public $firstname;

    /**
     * @ORM\Column(type="string");
     */
    public $lastname;

    /**
     * @ORM\Column(type="string");
     */
    public $password;

    /**
     * @ORM\Column(type="string");
     */
    public $telephone;

    /**
     * @ORM\Column(type="string");
     */
    public $email;

    /**
     * @ORM\Column(type="string");
     */
    public $description;

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

    public function getGridSql (array $gridInitialData = array())
    {
        // Get the where condition
        $where = $gridInitialData["where"];
        
        // Get the count
        $count = $gridInitialData["count"];
        
        // Get offset
        $offset = $gridInitialData["offset"];
        
        // Get the order
        $order = $gridInitialData["order"];
        
        $em = $this->getEntityManager();
        
        $to = $count + $offset;
        if ($order != null) {
            $order = "ORDER BY " . $order;
        }
        
        $sql = "SELECT
        u.*
        FROM
        user u
        WHERE u.afc_group_id=3 AND {$where} {$order} LIMIT {$offset},{$to}";
        return $sql;
    }

    public function getFilteredRecordSql (array $gridInitialData = array())
    {
        // Get the where condition
        $where = $gridInitialData["where"];
        
        $sql = "SELECT
            count(*) as total_records
			FROM
			user u
			WHERE u.afc_group_id=3 AND  {$where}";
        return $sql;
    }

    public function getTotalRecordSql ()
    {
        $sql = "SELECT
        count(*) as total_records
        FROM
        	user u
        WHERE u.afc_group_id=3";        
        return $sql;
    }
}