<?php

namespace models;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Heyward Jimenez
 * @version 1.0
 * @created 23-Ene-2012 02:39:38 p.m.
 * 
 * @Entity
 * @Table(name="translations")
 */
class Translations
{

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO") 
     */
    private $id;

    /**
     * @Column(type="string", length=20, nullable=false) 
     */
    private $origin;

    /**
     * @Column(type="integer", nullable=false) 
     */
    private $idOrigin;

    /**
     * @Column(type="string", length=5, nullable=false) 
     */
    private $language;

    /**
     * @Column(type="string", length=20, nullable=false) 
     */
    private $field;

    /**
     * @Column(type="string", length=500, nullable=false) 
     */
    private $translation;

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
     * Set origin
     *
     * @param string $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    /**
     * Get origin
     *
     * @return string 
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set idOrigin
     *
     * @param string $idOrigin
     */
    public function setIdOrigin($idOrigin)
    {
        $this->idOrigin = $idOrigin;
    }

    /**
     * Get idOrigin
     *
     * @return string 
     */
    public function getIdOrigin()
    {
        return $this->idOrigin;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * Set field
     *
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * Get field
     *
     * @return string 
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set translation
     *
     * @param string $translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
    }

    /**
     * Get translation
     *
     * @return string 
     */
    public function getTranslation()
    {
        return $this->translation;
    }
    
    public function toArray()
    {
        $return = array();
        $return['id']           = $this->getId();
        $return['origin']       = $this->getOrigin();
        $return['idOrigin']     = $this->getIdOrigin();
        $return['language']     = $this->getLanguage();
        $return['field']        = $this->getField();
        $return['translation']  = $this->getTranslation();
        
        return $return;
    }
}