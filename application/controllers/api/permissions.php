<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Permissions extends REST_Controller
{
	function permission_get()
    {
        try{
            if(!$this->get('id')){
            	$this->response(NULL, 400);
            }
            
            $permission = $this->em->find('models\Permissions', $this->get('id'));
            
            if($permission){
                $this->response(array("status"=>true, "data"=>$permission->toArray()), 200);
            } else {
                $this->response(array("status"=>false, 'error' => 'Register could not be found'), 404);
            }
        } catch (PDOException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\DBAL\DBALException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\ORM\ORMException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Exception $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        }
    }
    
    function permission_post()
    {
        try {
            if (!$this->post("id")){
            	$this->response(NULL, 400);
            }
        
            $section = $this->em->find('models\Sections', $this->post("idSection"));
            $permission = $this->em->find('models\Permissions', $this->post("id"));
            $permission->setInMenu($this->post("in_menu"));
            $permission->setPosition($this->post("position"));
            $permission->setSection($section);
            $this->em->persist($permission);
            $this->em->flush();
            
            $this->response(array('status' => true), 200);
        } catch (PDOException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\DBAL\DBALException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\ORM\ORMException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Exception $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        }
    }
    
    function change_inmenu_post()
    {
        try{
            if (!$this->post("id")){
            	$this->response(NULL, 400);
            }
            
            $permission = $this->em->find('models\Permissions', $this->post("id"));
            $inMenu     = ($permission->getInMenu() == AuthConstants::YES) ? AuthConstants::NO : AuthConstants::YES;
            $permission->setInMenu($inMenu);
            $this->em->persist($permission);
            $this->em->flush();
            $this->response(array('status' => true), 200);
        } catch (PDOException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\DBAL\DBALException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\ORM\ORMException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Exception $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        }
    }
    
    function up_position_post()
    {
        try {
            if (!$this->post("id")){
                $this->response(NULL, 400);
            }
        
            $this->loadRepository("Permissions");
            $permission = $this->em->find('models\Permissions', $this->post("id"));
            $position = $permission->getPosition();
            $permissionAux = $this->Permissions->findOneBy(array("section"=>$permission->getSection()->getId(), "position"=>$position+1), array("position" => "asc"));
            $permissions = $this->Permissions->findBy(array("section"=>$permission->getSection()->getId()), array("position" => "desc"));
            $firstPermision = $permissions[0];
            
            if ($position + 1 <= count($permissions)){
                $permission->setPosition($position + 1);
                $this->em->persist($permission);
                
                if (!empty($permissionAux)){
                    $permissionAux->setPosition($position);
                    $this->em->persist($permissionAux);
                }
            }
            
            $this->em->flush();
            
            $this->response(array('status' => true), 200);
        } catch (PDOException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\DBAL\DBALException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\ORM\ORMException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Exception $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        }
    }

    function down_position_post()
    {
        try {
            if (!$this->post("id")){
                $this->response(NULL, 400);
            }
        
            $this->loadRepository("Permissions");
            $permission = $this->em->find('models\Permissions', $this->post("id"));
            $position = $permission->getPosition();
            
            if ($position - 1 > 0 ){
                $permissionAux = $this->Permissions->findOneBy(array("section"=>$permission->getSection()->getId(), "position"=>$position-1), array("position" => "asc"));
                
                $permission->setPosition($position - 1);
                $this->em->persist($permission);
                
                if (!empty($permissionAux)){
                    $permissionAux->setPosition($position);
                    $this->em->persist($permissionAux);
                }
                
                $this->em->flush();
            }
            
            $this->response(array('status' => true), 200);
        } catch (PDOException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\DBAL\DBALException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\ORM\ORMException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Exception $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        }
    }
}