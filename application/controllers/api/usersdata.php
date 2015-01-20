<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class UsersData extends REST_Controller
{
	function userdata_get()
    {
        try {
            if(!$this->get('id')){
            	$this->response(NULL, 400);
            }
            
            $userData = $this->em->find('models\UsersData', $this->get('id'));
            
            if($userData){
                $this->response(array("status"=>true, "data"=>$userData->toArray()), 200);
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
    
    function userdata_post()
    {
        try {
            if (!$this->post("id")){
            	$this->response(NULL, 400);
            }
        
            $city = $this->em->find('models\Cities', $this->post('idCity'));
            $profile = $this->em->find('models\Profiles', $this->put('idProfile'));
            
            $userData = $this->em->find('models\UsersData', $this->post("id"));
            $user = $userData->getUser();
            
            $user->setProfile($profile);
            $user->setCity($city);
            $user->setName($this->post("name"));
            $user->setLastName($this->post("lastName"));
            $user->setEmail($this->post("email"));
            $user->setLanguage($this->post("language"));
            $user->setPassword(md5($this->post("password")));
            $user->setAdmin(AuthConstants::ADMIN_KO);
            $this->em->persist($user);
            
            $userData->setIdentification($this->post("identification"));
            $this->em->persist($userData);
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

    function userdata_put()
    {
        try {
            $city = $this->em->find('models\Cities', $this->put('idCity'));
            $profile = $this->em->find('models\Profiles', $this->put('idProfile'));
            
            $user = new models\Users();
            $user->setProfile($profile);
            $user->setCity($city);
            $user->setName($this->put("name"));
            $user->setLastName($this->put("lastName"));
            $user->setEmail($this->put("email"));
            $user->setLanguage($this->put("language"));
            $user->setPassword(md5($this->put("password")));
            $user->setAdmin(AuthConstants::ADMIN_KO);
            $user->setCreationDate(new DateTime());
            $this->em->persist($user);
            
            $userData = new models\UsersData();
            $userData->setIdentification($this->put("identification"));
            $userData->setUser($user);
            $this->em->persist($userData);
            
            $this->em->flush();
            
            $id = $userData->getId();
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

    function userdata_delete()
    {
        try {
            if (!$this->delete("id")){
            	$this->response(NULL, 400);
            }
            
            $userData   = $this->em->find('models\UsersDara', $this->delete("id"));
            $user       = $userData->getUser();
            $superUser  = ($user->getAdmin() == AuthConstants::ADMIN_OK);
            
            if ($superUser) {
                $this->response(array('status' => false, 'warning' => "user_superuser"), 400);
            }
            
            if ($superUser == false) {
                    $this->em->remove($userData);
                    $this->em->remove($user);
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

    function exist_user_get()
    {
        try {
            if(!is_numeric($this->get('id')) || !$this->get('email')){
                $this->response(NULL, 400);
            }
            
            $this->loadRepository("Users");
            
            $user = new models\Users();
            
            if ($this->get('id') > 0) {
                $userData = $this->em->find('models\UsersData', $this->get('id'));
                $user = $userData->getUser();
            }
            
            $email      = $this->get('email');
            $edition    = ($this->get('id') > 0);
            $different  = ($email != $user->getEmail());
            $userAux    = $this->Users->findOneBy(array("email" => $email));
            $exist      = (empty($userAux) == false);
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
}