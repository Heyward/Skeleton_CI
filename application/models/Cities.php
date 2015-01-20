<?php

namespace models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Heyward Jimenez
 * @version 1.0
 * @created 23-Ene-2012 02:39:38 p.m.
 * 
 * @Entity
 * @Table(name="cities")
 */
class Cities
{

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO") 
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Countries")
     */
    private $country;

    /**
     * @Column(type="string", length=100, nullable=false) 
     */
    private $name;

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
     * Set country
     *
     * @param models\Countries $country
     */
    public function setCountry(\models\Countries $country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return models\Countries 
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    public function toArray()
    {
        $return = array();
        $return['id']       = $this->getId();
        $return['name']     = $this->getName();
        $return['country']  = $this->getCountry()->toArray();
        
        return $return;
    }
}