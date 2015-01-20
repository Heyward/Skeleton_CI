<?php
function getClassesFromFile ()
{
    $return = array();
    
    $DS          = DIRECTORY_SEPARATOR;
    $pathModules = dirname(__FILE__) . $DS . ".." .$DS . "modules" . $DS;
    $directories = Soporte::leeDirectorio($pathModules);
    
    foreach ($directories as $key => $value){
        $dirController = $pathModules . $key . $DS . "controllers" . $DS;
        $files       = Soporte::leeDirectorio($dirController);
        
        foreach ($files as $key => $value){
            $classes = file_get_php_classes($dirController . $key);
            
            foreach ($classes as $aClass => $methods){
                $return[$aClass] = array();
                foreach ($methods as $aMethod){
                    array_push($return[$aClass] , $aMethod);
                }
            }
        }
    }
    
    return $return;
}

function file_get_php_classes ($filepath, $onlypublic = true)
{
    $php_code = file_get_contents($filepath);
    $classes  = get_php_classes($php_code, $onlypublic);
    return $classes;
}
    
function get_php_classes ($php_code, $onlypublic)
{
    $classes    = array();
    $methods    = array();
    $tokens     = token_get_all($php_code);
    $count      = count($tokens);
    
    for ($i = 2; $i < $count; $i ++) {
        if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
            $class_name = $tokens[$i][1];
            $methods[$class_name] = array();
        }
        
        if ($tokens[$i - 2][0] == T_FUNCTION && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
            if ($onlypublic) {
                if (! in_array($tokens[$i - 4][0], array(T_PROTECTED, T_PRIVATE))) {
                    $method_name = $tokens[$i][1];
                    $methods[$class_name][] = $method_name;
                }
            } 
            else {
                $method_name = $tokens[$i][1];
                $methods[$class_name][] = $method_name;
            }
        }
    }
    
    return $methods;
}

function createPermissions($classes){
    $CI = &get_instance();
    $CI->load->library('doctrine');
    
    $position   = 0;
    
    $repoSections = $CI->doctrine->em->getRepository('models\\Sections');
    $repoPermissions = $CI->doctrine->em->getRepository('models\\Permissions');
    
    foreach ($classes as $aClass => $methods){
        $position++;
        
        $section = $repoSections->findBy(array("label"=>$aClass));
        if (empty($section)){
            $section = new models\Sections();
            $section->setLabel($aClass);
            $section->setPosition($position);
            $CI->doctrine->em->persist($section);
            
            $methodsNoMenu = array();
            $methodsNoMenu[] = "setListParameters";
            $methodsNoMenu[] = "delete";
            $methodsNoMenu[] = "persist";
            $methodsNoMenu[] = "getList";
            $methodsNoMenu[] = "__construct";
            
            $index = 0;
            foreach ($methods as $aMethod){
                $index++;
                $permission = $repoPermissions->findBy(array("url"=>strtolower($aClass."/".$aMethod)));
                if (empty($permission)){
                    if (in_array($aMethod, $methodsNoMenu) == false){
                        $permission = new models\Permissions();
                        $permission->setLabel($aClass."_".$aMethod);
                        $permission->setInMenu(AuthConstants::YES);
                        $permission->setPosition($index);
                        $permission->setUrl(strtolower($aClass."/".$aMethod));
                        $permission->setSection($section);
                        $CI->doctrine->em->persist($permission);
                    }
                }
            }
        }

    }
    
    $CI->doctrine->em->flush();
}

function updatePermissions($classes){
    $CI = &get_instance();
    $CI->load->library('doctrine');
    
    $position   = 0;
    
    $repoSections = $CI->doctrine->em->getRepository('models\\Sections');
    $repoPermissions = $CI->doctrine->em->getRepository('models\\Permissions');
    
    foreach ($classes as $aClass => $methods){
        $position++;
        
        $section = $repoSections->findOneBy(array("label"=>$aClass));
        if (empty($section) == false){
            $methodsNoMenu = array();
            $methodsNoMenu[] = "setListParameters";
            $methodsNoMenu[] = "delete";
            $methodsNoMenu[] = "persist";
            $methodsNoMenu[] = "getList";
            $methodsNoMenu[] = "__construct";
            
            $index = 0;
            foreach ($methods as $aMethod){
                $index++;
                $permission = $repoPermissions->findBy(array("url"=>strtolower($aClass."/".$aMethod)));
                if (empty($permission)){
                    if (in_array($aMethod, $methodsNoMenu) == false){
                        $permission = new models\Permissions();
                        $permission->setLabel($aClass."_".$aMethod);
                        $permission->setInMenu(AuthConstants::NO);
                        $permission->setPosition($index);
                        $permission->setUrl(strtolower($aClass."/".$aMethod));
                        $permission->setSection($section);
                        $CI->doctrine->em->persist($permission);
                    }
                }
            }
        }

    }
    
    $CI->doctrine->em->flush();
}