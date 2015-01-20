<?php

namespace models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Heyward Jimenez
 * @version 1.0
 * @created 23-Ene-2012 02:39:38 p.m.
 * 
 * @Entity
 * @Table(name="profiles")
 */
class Profiles
{
    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO") 
     */
    private $id;

    /**
     * @ManyToMany(targetEntity="Permissions")
     */
    private $permissions;

    /**
     * @OneToMany(targetEntity="Users", mappedBy="profile")
     */
    private $users;

    /**
     * @Column(type="string", length=100, unique=true, nullable=false) 
     */
    private $name;

    /**
     * @Column(type="text", length=100, nullable=true) 
     */
    private $description;

    public function __construct()
    {
        $this->permission = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add permission
     *
     * @param models\Permissions $permission
     */
    public function addPermission(\models\Permissions $permission)
    {
        $this->permissions[] = $permission;
    }

    /**
     * Get permissions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add user
     *
     * @param models\Users $user
     */
    public function addUser(\models\Users $user)
    {
        $this->users[] = $user;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    public function toArray()
    {
        $return = array();
        $return['id']           = $this->getId();
        $return['name']         = $this->getName();
        $return['description']  = $this->getDescription();
        
        return $return;
    }
}