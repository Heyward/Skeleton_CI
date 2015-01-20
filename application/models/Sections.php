<?php

namespace models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Heyward Jimenez
 * @version 1.0
 * @created 23-Ene-2012 02:39:38 p.m.
 * 
 * @Entity
 * @Table(name="sections")
 */
class Sections
{

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO") 
     */
    private $id;

    /**
     * @Column(type="string", length=100, nullable=false) 
     */
    private $label;

    /**
     * @Column(type="integer", nullable=false) 
     */
    private $position;
    
    /**
     * @OneToMany(targetEntity="Permissions", mappedBy="section")
     */
    private $permissions;
    
    /**
     * @Column(type="string", length=100, nullable=true) 
     */
    private $icon;
    
    public function __construct()
    {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set icon
     *
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set position
     *
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Add permissions
     *
     * @param models\Permissions $permissions
     */
    public function addPermissions(\models\Permissions $permissions)
    {
        $this->permissions[] = $permissions;
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
    
    public function toArray()
    {
        $return = array();
        
        $permissions = array();
        foreach ($this->getPermissions() as $aPermissions ) {
            $permissions[] = $aPermissions->toArray(false);
        }
        
        $return['id']           = $this->getId();
        $return['permissions']  = $permissions;
        $return['label']        = $this->getLabel();
        $return['position']     = $this->getPosition();
        
        return $return;
    }
}