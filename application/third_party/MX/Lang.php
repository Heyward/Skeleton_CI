<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Language class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Lang.php
 *
 * @copyright	Copyright (c) 2011 Wiredesignz
 * @version 	5.4
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * */
class MX_Lang extends CI_Lang {

    var $language = array();
    var $is_loaded = array();
    var $idiom;
    var $line;
    var $CI;

    function __construct() {
        parent::__construct();
    }

    /**
     * Load a language file
     *
     * @access    public
     * @param    mixed    the name of the language file to be loaded. Can be an array
     * @param    string    the language (english, etc.)
     * @return    mixed
     */
    function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
        if ($idiom == 'db') {
            $this->idiom = $langfile;

            if (in_array($langfile, $this->is_loaded, TRUE)) {
                return;
            }

            if ($langfile == '') {
                $deft_lang = CI::$APP->config->item('language');
                $idiom = ($deft_lang == '') ? 'en' : $deft_lang;

                $this->idiom = $idiom;
            }

            $database_lang = $this->_get_from_db();
            if (!empty($database_lang)) {
                $lang = $database_lang;
            } else {
                show_error('Unable to load the requested language file: language/' . $langfile);
            }

            if ($return == TRUE) {
                return $lang;
            }

            $this->is_loaded[] = $langfile;
            $this->language = array_merge($this->language, $lang);
            unset($lang);

            log_message('debug', 'Language file loaded: language/' . $idiom . '/' . $langfile);
            return TRUE;
        } else {
            if (is_array($langfile)) {
                foreach ($langfile as $_lang)
                    $this->load($_lang);
                return $this->language;
            }

            $deft_lang = CI::$APP->config->item('language');
            $lang = ($idiom == '') ? $deft_lang : $idiom;
            $idiom = ($lang == '') ? $deft_lang : $idiom;

            if (in_array($langfile . '_lang' . EXT, $this->is_loaded, TRUE))
                return $this->language;

            $_module = CI::$APP->router->fetch_module();

            list($path, $_langfile) = Modules::find($langfile . '_lang', $_module, 'language/' . $idiom . '/');

            if ($path === FALSE) {
                if ($lang = parent::load($langfile, $lang, $return, $add_suffix, $alt_path))
                    return $lang;
            } else {
                if ($lang = Modules::load_file($_langfile, $path, 'lang')) {
                    if ($return)
                        return $lang;
                    $this->language = array_merge($this->language, $lang);
                    $this->is_loaded[] = $langfile . '_lang' . EXT;
                    unset($lang);
                }
            }

            return $this->language;
        }
    }

    /**
     * Load a language from database
     *
     * @access    private
     * @return    array
     */
    private function _get_from_db() {
        $CI = & get_instance();

        $CI->db->select('*');
        $CI->db->from('sys_language_label');
        $CI->db->where('language', $this->idiom);

        $query = $CI->db->get()->result();

        foreach ($query as $row) {
            $return[$row->variable] = $row->text;
        }

        unset($CI, $query);
        return $return;
    }
}