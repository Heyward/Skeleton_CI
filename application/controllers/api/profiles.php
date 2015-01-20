<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Profiles extends REST_Controller
{
	function profile_get()
    {
        try{
            if(!$this->get('id')){
            	$this->response(NULL, 400);
            }
    
            $profile = $this->em->find('models\Profiles', $this->get('id'));
            
            if($profile){
                $this->response(array("status"=>true, "data"=>$profile->toArray()), 200);
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
    
    function profile_post()
    {
        try {
            if (!$this->post("id")){
            	$this->response(NULL, 400);
            }
        
            $profile = $this->em->find('models\Profiles', $this->post("id"));
            $profile->setName($this->post("name"));
            $profile->setDescription($this->post("description"));
            $this->em->persist($profile);
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

    function profile_put()
    {
        try {
            $profile = new models\Profiles();
            $profile->setName($this->put("name"));
            $profile->setDescription($this->put("description"));
            $this->em->persist($profile);
            $this->em->flush();
            $id = $profile->getId();
            $this->response(array('status' => true, 'id' => $id), 200);
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

    function profile_delete()
    {
        try {
            if (!$this->delete("id")){
            	$this->response(NULL, 400);
            }
            
            $this->loadRepository("Users");
            $profile    = $this->em->find('models\Profiles', $this->delete("id"));
            $users      = $this->Users->findBy(array("profile" => $this->delete("id")));
            $haveUsers  = (count($users) > 0);
            
            if ($haveUsers) {
                $this->response(array('status' => false, 'warning' => "profile_have_users"), 400);
            }
            
            if ($haveUsers == false) {
                    $profile->getPermissions()->clear();
                    $this->em->persist($profile);
                    $this->em->remove($profile);
                    $this->em->flush();
                    $this->response(array('status' => true), 200);
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
    
    function exist_profile_get()
    {
        try{
            if(!is_numeric($this->get('id')) || !$this->get('name')){
                $this->response(NULL, 400);
            }
            
            $this->loadRepository("Profiles");
            
            $profile = new models\Profiles();
            
            if ($this->get('id') > 0) {
                $profile = $this->em->find('models\Profiles', $this->get('id'));
            }
    
            $name       = $this->get('name');
            $edition    = ($this->get('id')  > 0);
            $different  = ($name!= $profile->getName());
            $profileAux = $this->Profiles->findOneBy(array("name" => $name));
            $exist      = (empty($profileAux) == false);
            $errorExist = ($edition && $different && $exist) || ($edition == false && $exist);
            
            $this->response(array('exist' => !$errorExist), 200);
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
    
    function permissions_put()
    {
        try {
            if(!$this->put('id') ){
                $this->response(NULL, 400);
            }
        
            $permissionsPost= $this->put('permissions');
            $profile        = $this->em->find('models\Profiles', $this->put('id'));
            $permissions    = $profile->getPermissions();
            
            $permissions->clear();
            $this->em->persist($profile);
            
            foreach ($permissionsPost as $aPermission) {
                if ($aPermission > 0){
                    $permission = $this->em->find('models\Permissions', $aPermission);
                    $profile->addPermission($permission);
                    $this->em->persist($profile);
                }
            }
            
            $this->em->flush();
            $this->response(array('status' => true), 200);
        } catch (PDOException $e) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\DBAL\DBALException $exc) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Doctrine\ORM\ORMException $exc) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        } catch (Exception $exc) {
            $this->response(array('status' => false, 'error' => $e->getMessage()), 400);
        }
    }
}