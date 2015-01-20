<?php

/**
 * metodo para verificar si el usuario tiene permisos
 * para realizar una accion.
 *
 * @param string $metodName nombre del metodo que ejecuta la accion
 * @param string $clase nombre del controlador (opcional)
 * @param string $directorio nombre de la carpeta donde se encuentra el controlador (opcional)
 * @return boolean 
 */
function hasRights($method, $class = "")
{
    $ci = &get_instance();
    $ci->load->library('doctrine');

    if ($ci->session->userdata(AuthConstants::ADMIN) == AuthConstants::ADMIN_OK){
        return true;
    }
    
    if (empty($class)) {
        $class = $ci->router->class;
    }

    $url            = strtolower($class . "/" . $method);
    $user           = $ci->doctrine->em->find('models\Users', $ci->session->userdata(AuthConstants::USER_ID));
    $profile        = $user->getProfile();
    $permissions    = $profile->getPermissions();
    $permitted      = false;

    foreach ($permissions as $aPermission) {
        $equal = ($url == $aPermission->getUrl());
        if ($equal) {
            $permitted = true;
            break;
        }
    }

    return $permitted;
}
