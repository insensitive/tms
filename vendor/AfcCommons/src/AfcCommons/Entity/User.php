<?php
namespace AfcCommons\Entity;

use Doctrine\ORM\Mapping as ORM;
use AfcCommons\Entity\Entity;

/**
 * User
 * @ORM\Table(name = "user")
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 *
 * @property int $last_updated_by
 * @property datetime $last_updated_at
 * @property int $created_by
 * @property datetime $created_at
 * @property string $password
 * @property string $username
 * @property int $group_id
 * @property int $user_id
 */
abstract class User extends Entity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="AfcCommons\Entity\Group",inversedBy="users")
     * @ORM\JoinColumn(name="afc_group_id", referencedColumnName="afc_group_id")
     */
    public $group;

    /**
     * @ORM\Column(type="string",name="username");
     */
    public $firstname;
    
    /**
     * @ORM\Column(type="string",name="username");
     */
    public $lastname;
    //
    // @ORM\Column(type="string",name="username");
    //
    //public $username;

    /**
     * @ORM\Column(type="string");
     */
    public $password;

    /**
     * @ORM\Column(type="datetime");
     */
    public $created_at;

    /**
     * @ORM\Column(type="integer");
     */
    public $created_by;

    /**
     * @ORM\Column(type="datetime");
     */
    public $last_updated_at;

    /**
     * @ORM\Column(type="integer");
     */
    public $last_updated_by;
}