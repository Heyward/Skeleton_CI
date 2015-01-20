<?php

namespace models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Heyward Jimenez
 * @version 1.0
 * @created 23-Ene-2012 02:39:38 p.m.
 * 
 * @Entity(repositoryClass="Repositories\UsersRepository")
 * @Table(name="users")
 */
class Users
{
    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO") 
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Cities")
     */
    private $city;

    /**
     * @ManyToOne(targetEntity="Profiles", inversedBy="users")
     */
    private $profile;

    /**
     * @Column(type="string", length=100, nullable=false) 
     */
    private $name;

    /**
     * @Column(type="string", length=100, nullable=false) 
     */
    private $last_name;

    /**
     * @Column(type="string", unique=true, length=100, nullable=false) 
     */
    private $email;

    /**
     * @Column(type="string",  length=100, nullable=false) 
     */
    private $password;
    
    /**
     * @Column(type="datetime", nullable=true) 
     */
    private $last_access;

    /**
     * @Column(type="integer", nullable=true) 
     */
    private $admin;
    
    /**
     * @OneToOne(targetEntity="UsersData", mappedBy="user", cascade={"all"})
     */
     private $user_data;
     
     /**
     * @Column(type="string", length=5, nullable=false) 
     */
     private $language;
     
     /**
     * @Column(type="date", nullable=true) 
     */
     private $creationDate;
     
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
     * Set last_name
     *
     * @param string $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set last_access
     *
     * @param integer $last_access
     */
    public function setLastAccess($last_access)
    {
        $this->last_access = $last_access;
    }

    /**
     * Get last_access
     *
     * @return integer 
     */
    public function getLastAccess()
    {
        return $this->last_access;
    }

    /**
     * Set admin
     *
     * @param integer $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * Get admin
     *
     * @return integer  
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set city
     *
     * @param models\Cities $city
     */
    public function setCity(\models\Cities $city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return models\Cities 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set profile
     *
     * @param models\Profiles $profile
     */
    public function setProfile(\models\Profiles $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Get profile
     *
     * @return models\Profiles
     */
    public function getProfile()
    {
        return $this->profile;
    }
    
    /**
     * Set user_data
     *
     * @param models\UsersData $user_data
     */
    public function setUserData(\models\UsersData $user_data)
    {
        $this->user_data = $user_data;
    }
    
    /**
     * Get datauser
     *
     * @return models\UsersData 
     */
    public function getUserData()
    {
        return $this->user_data;
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
     * Set creationDate
     *
     * @param string $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * Get creationDate
     *
     * @return string 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
    
    public function toArray()
    {
        $return = array();
        $return['id']           = $this->getId();
        $return['name']         = $this->getName();
        $return['last_name']    = $this->getLastName();
        $return['email']        = $this->getEmail();
        $return['language']     = $this->getLanguage();
        $return['profile']      = $this->getProfile()->toArray();
        $return['city']         = $this->getCity()->toArray();
        $return['user_data']    = array();
        $return['creationDate'] = "";
        
        if (is_null($this->getUserData()) == false){
            $return['user_data']    = $this->getUserData()->toArray(false);
        }
        
        if (is_null($this->getCreationDate()) == false){
            $return['creationDate']    = $this->getCreationDate()->format("Y-m-d");
        }
        
        return $return;
    }
}