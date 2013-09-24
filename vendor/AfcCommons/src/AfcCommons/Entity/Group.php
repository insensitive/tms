<?php

namespace AfcCommons\Entity;

use Doctrine\ORM\Mapping as ORM;
use AfcCommons\Entity\Entity;

/**
 * Groups for users.
 *
 * @ORM\Entity
 * @ORM\Table(name="afc_group")
 *
 * @property string $name
 * @property int $parent_group_id
 * @property int $group_id
 */
class Group extends Entity {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\Column(name="afc_group_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $group_id;
	
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $parent_group_id;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;
	
	/**
	 * @ORM\OneToMany(targetEntity="User\Entity\User",mappedBy="group")
	 */
	protected $users;
}