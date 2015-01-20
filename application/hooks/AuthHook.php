<?php
if(!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

class AuthHook
{
    private $em;
    
    public function auth()
    {
        $CI =& get_instance();
        $CI->load->library ("session");
        $CI->load->helper  ("url");
        
        $this->em = $CI->doctrine->em;
        
        $noauth = array();
        $public = false;
        
        $class      = $CI->router->class;
        $controller = new $class();
        $method     = $CI->router->method;
        $dir = $CI->router->directory;
        
        if (isset ($controller->sauth_noauth) && is_array($controller->sauth_noauth)){
            $noauth = $controller->sauth_noauth;
        }
        
        if (isset ($controller->public) && $controller->public){
            $public = true;
        }
        
        $isApi = (substr($dir, 0, -1) == "api");
        $noApi = ($isApi == false);
        $isAjax = $CI->input->is_ajax_request();
        $isPublic = (in_array($method, $noauth) || $public);
        $isReport = ("../modules/reports/controllers/" == $dir);
        
        if ($noApi && $isAjax == false && $isReport == false){
            $method = strtolower($method);
            $class = strtolower($class);
            $permission = $class."/".$method;
            $user       = $CI->session->userdata (AuthConstants::USER_ID);
            $admin      = ($CI->session->userdata (AuthConstants::ADMIN) == AuthConstants::ADMIN_OK);
            $logged     = ($user > 0);
            $isLogin    = ($class == "login");
            $isForbbiden = ($method == "forbbiden");
            $isLogout    = ($method == "logout");
                        
            if ($isLogout == false){
                if ($logged == false && $isLogin == false && $isPublic == false){
                    redirect ("/login/index/");
                    exit();
                }
                
                if ($logged && $isLogin){
                    redirect ("/home/index/");
                    exit();
                }
                                
                if ($isForbbiden == false && $logged){
                    $authorized = $this->em->getRepository('models\Users')->permitted($user, $permission);
                    $permitted  = ($authorized || $admin || $method == 'getlist' || $method == 'persist' || $method == 'delete');

                    if ($permitted == false){
                        redirect("/home/forbbiden/");
                        exit();
                    }
                }
                
                
            }
        }
    }
}
?>