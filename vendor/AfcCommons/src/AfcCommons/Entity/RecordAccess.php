<?php
namespace AfcCommons\Entity;

use Doctrine\ORM\Mapping as ORM;
use AfcCommons\Entity\Entity;

/**
 * Group Table Access
 *
 * @ORM\Entity
 * @ORM\Table(name="afc_record_access")
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class RecordAccess extends Entity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="afc_record_access_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $record_access_id;

    /**
     * @ORM\Column(type="string");
     */
    protected $table_name;

    /**
     * @ORM\Column(type="string");
     */
    protected $primary_key_column;

    /**
     * @ORM\Column(type="integer");
     */
    protected $primary_key;

    /**
     * @ORM\Column(type="integer");
     */
    protected $user_id;

    /**
     * @ORM\Column(type="string",
     * columnDefinition="ENUM('0','1','2','3','4','5','6','7')")
     */
    protected $user_access_mode;

    /**
     * @ORM\Column(type="string",
     * columnDefinition="ENUM('0','1','2','3','4','5','6','7')")
     */
    protected $group_access_mode;

    /**
     * @ORM\Column(type="string",
     * columnDefinition="ENUM('0','1','2','3','4','5','6','7')")
     */
    protected $other_access_mode;
}