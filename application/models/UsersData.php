<?php

namespace models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Heyward Jimenez
 * @version 1.0
 * @created 23-Ene-2012 02:39:38 p.m.
 * 
 * @Entity
 * @Table(name="users_data")
 */
class UsersData
{
    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO") 
     */
    private $id;
    
    /**
     * @OneToOne(targetEntity="Users", inversedBy="user_data", cascade={"all"})
     */
    private $user;

    /**
     * @Column(type="string", nullable=false)
     */
    private $identification;

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
     * Set user
     *
     * @param models\Users $user
     */
    public function setUser(\models\Users $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return models\Users 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set identification
     *
     * @param string $identification
     * @return UsersData
     */
    public function setIdentification($identification)
    {
        $this->identification = $identification;
    
        return $this;
    }

    /**
     * Get identification
     *
     * @return string 
     */
    public function getIdentification()
    {
        return $this->identification;
    }

    public function toArray($user = true)
    {
        $return = array();
        
        $return['id']               = $this->getId();
        $return['user']             = $this->getUser()->toArray();
        $return['identification']   = $this->getIdentification();
        
        return $return;
    }
}