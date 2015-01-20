<?php

namespace models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Heyward Jimenez
 * @version 1.0
 * @created 23-Ene-2012 02:39:38 p.m.
 * 
 * @Entity
 * @Table(name="permissions")
 */
class Permissions
{

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO") 
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Sections")
     */
    private $section;

    /**
     * @Column(type="string", length=100, nullable=false) 
     */
    private $label;

    /**
     * @Column(type="text", nullable=false) 
     */
    private $url;

    /**
     * @Column(type="text", nullable=true) 
     */
    private $in_menu;

    /**
     * @Column(type="integer", nullable=true) 
     */
    private $position;

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
     * Set url
     *
     * @param text $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return text 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set in_menu
     *
     * @param text $in_menu
     */
    public function setInMenu($in_menu)
    {
        $this->in_menu = $in_menu;
    }

    /**
     * Get in_menu
     *
     * @return text 
     */
    public function getInMenu()
    {
        return $this->in_menu;
    }
    public function getIn_Menu()
    {
        return $this->in_menu;
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
     * Set section
     *
     * @param models\Sections $section
     */
    public function setSection(\models\Sections $section)
    {
        $this->section = $section;
    }

    /**
     * Get section
     *
     * @return models\Sections 
     */
    public function getSection()
    {
        return $this->section;
    }
    
    public function toArray($section = true)
    {
        $return = array();
        $return['id']       = $this->getId();
        $return['label']    = $this->getLabel();
        $return['url']      = $this->getUrl();
        $return['in_menu']  = $this->getInMenu();
        $return['position'] = $this->getPosition();

        if ($section){
            $return['section'] = $this->getSection()->toArray();
        }
        
        return $return;
    }
}