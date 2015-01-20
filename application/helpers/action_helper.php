<?php
class Action
{
    private $class;
    private $method;
    private $label;
    private $url;

    public function __construct ($class = "", $method= "", $label = "", $redirect = true)
    {
        $this->class    = $class;
        $this->method   = $method;
        $this->label    = $label;
        $this->url      = $redirect;
    }
    
	/**
     * @return the $class
     */
    public function getClass ()
    {
        return $this->class;
    }

	/**
     * @return the $method
     */
    public function getMethod ()
    {
        return $this->method;
    }

	/**
     * @return the $label
     */
    public function getLabel ()
    {
        return $this->label;
    }

	/**
     * @return the $url
     */
    public function getUrl ()
    {
        return $this->url;
    }

	/**
     * @param $class the $class to set
     */
    public function setClass ($class)
    {
        $this->class = $class;
    }

	/**
     * @param $method the $method to set
     */
    public function setMethod ($method)
    {
        $this->method = $method;
    }

	/**
     * @param $label the $label to set
     */
    public function setLabel ($label)
    {
        $this->label = $label;
    }

	/**
     * @param $url the $url to set
     */
    public function setUrl ($url)
    {
        $this->url = $url;
    }
}