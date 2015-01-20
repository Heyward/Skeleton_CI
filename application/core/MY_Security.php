<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Security extends CI_Security
{
    /**
     * Verify Cross Site Request Forgery Protection
     *
     * @return  object
     */
    public function csrf_verify()
    {
        // If no POST data exists we will set the CSRF cookie
        if (count($_POST) == 0)
        {
            return $this->csrf_set_cookie();
        }
        
        /******************************************************
         * ----------------------------------------------------
         * ADDED FOR EXCLUDE SOME PAGES AND APO FOR VERIFY CSRF
         * ----------------------------------------------------
         ******************************************************/
        $CFG =& load_class('Config', 'core');
        $URI =& load_class('URI', 'core');
        $exc = explode("/", $URI->uri_string());
        $excludes = $CFG->item("csrf_exlude_uris");
        if ( in_array($URI->uri_string(), $excludes) || $exc[0] == "api" ){
            return;
        }
        /*/*****************************************************
         * ----------------------------------------------------
         * END ADDED
         * ----------------------------------------------------
         *******************************************************/
        
        // Do the tokens exist in both the _POST and _COOKIE arrays?
        if ( ! isset($_POST[$this->_csrf_token_name]) OR
             ! isset($_COOKIE[$this->_csrf_cookie_name]))
        {
            $this->csrf_show_error();
        }

        // Do the tokens match?
        if ($_POST[$this->_csrf_token_name] != $_COOKIE[$this->_csrf_cookie_name])
        {
            $this->csrf_show_error();
        }

        // We kill this since we're done and we don't want to
        // polute the _POST array
        unset($_POST[$this->_csrf_token_name]);

        // Nothing should last forever
        unset($_COOKIE[$this->_csrf_cookie_name]);
        $this->_csrf_set_hash();
        $this->csrf_set_cookie();

        log_message('debug', "CSRF token verified ");

        return $this;
    }
}
