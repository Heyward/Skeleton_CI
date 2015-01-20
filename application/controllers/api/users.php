<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Users extends REST_Controller
{
	function user_get()
    {
        try{
            if(!$this->get('id')){
            	$this->response(NULL, 400);
            }
            
            $user = $this->em->find('models\Users', $this->get('id'));
            
            if($user){
                $this->response(array("status"=>true, "data"=>$user->toArray()), 200);
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
    
    function user_post()
    {
        try {
            if (!$this->post("id")){
                $this->response(NULL, 400);
            }
        
            $city = $this->em->find('models\Cities', $this->post('idCity'));
            $profile = $this->em->find('models\Profiles', $this->post('idProfile'));
            $user = $this->em->find('models\Users', $this->post("id"));
            $user->setProfile($profile);
            $user->setCity($city);
            $user->setName($this->post("name"));
            $user->setLastName($this->post("lastName"));
            $user->setEmail($this->post("email"));
            $user->setLanguage($this->post("language"));
            $user->setPassword(md5($this->post("password")));
            $user->setAdmin(AuthConstants::ADMIN_KO);

            $this->em->persist($user);
            
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

    function user_put()
    {
        try {
            $user = new models\Users();
            $city = $this->em->find('models\Cities', $this->put('idCity'));
            $profile = $this->em->find('models\Profiles', $this->put('idProfile'));
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
            $this->em->flush();
            
            $this->em->flush();
            $id = $user->getId();
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

    function user_delete()
    {
        try {
            if (!$this->delete("id")){
            	$this->response(NULL, 400);
            }
            
            $user = $this->em->find('models\Users', $this->delete("id"));
            $superUser= ($user->getAdmin() == AuthConstants::ADMIN_OK);
            
            if ($superUser) {
                $this->response(array('status' => false, 'warning' => "user_superuser"), 400);
            }
            
            if ($superUser == false) {
                    if ($user->getUserData()){
                        $this->em->remove($user->getUserData());
                    }
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
        try{
            if(!is_numeric($this->get('id')) || !$this->get('email')){
                $this->response(NULL, 400);
            }
            
            $this->loadRepository("Users");
            
            $user = new models\Users();
            
            if ($this->get('id') > 0) {
                $user = $this->em->find('models\Users', $this->get('id'));
            }
    
            $email      = $this->get('email');
            $edition    = ($this->get('id')  > 0);
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

    function mydata_post()
    {
        try {
            if (!$this->post("id")){
                $this->response(NULL, 400);
            }
        
            $city = $this->em->find('models\Cities', $this->post('idCity'));
            $user = $this->em->find('models\Users', $this->post("id"));
            $user->setCity($city);
            $user->setName($this->post("name"));
            $user->setLastName($this->post("lastName"));
            $user->setEmail($this->post("email"));
            $user->setLanguage($this->post("language"));
            $this->em->persist($user);
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

    function change_password_post()
    {
        try {
            if (!$this->post("id")){
                $this->response(NULL, 400);
            }
        
            $user = $this->em->find('models\Users', $this->post("id"));
            $user->setPassword(md5($this->post("new_password")));
            $this->em->persist($user);
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

    function verify_password_get()
    {
        try{
            if(!is_numeric($this->get('id')) || !$this->get('password')){
                $this->response(NULL, 400);
            }
            
            $this->loadRepository("Users");
            $user = $this->Users->findBy(array('password'=>md5($this->get('password')), 'id'=>$this->get('id')));
            $exist = !empty($user);
            
            $this->response(array('exist' => $exist), 200);
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
    
    function autocomplete_get(){
        try{
            if(!$this->get('term')){
                $this->response(NULL, 400);
            }
            
            $term = $this->get('term');
            $country = $this->get('country');
            $dql  = "SELECT u.id id, u.name name, u.last_name last_name, u.email email, ud.identification identification "; 
            $dql .= "FROM models\\UsersData ud ";
            $dql .= "JOIN ud.user u ";
            $dql .= "JOIN u.city c ";
            $dql .= "JOIN c.country co ";
            $dql .= "WHERE (u.name LIKE '%".$term."%' ";
            $dql .= "OR u.last_name LIKE '%".$term."%' ";
            $dql .= "OR u.email LIKE '%".$term."%' ";
            $dql .= "OR ud.identification LIKE '%".$term."%') ";
            
            if ($country > 0){
                $dql .= "AND co.id = ".$country." ";
            }
            
            $query  = $this->em->createQuery($dql);
            $result = $query->getResult();
            
            $data = array();
            
            foreach ($result as $row){
                $data[] = array("id"=>$row["id"], "label"=>lang("id").":" . $row["identification"] . " ".lang("name").":" . $row["name"] . " " . $row["last_name"] . " ".lang("email").":" . $row["email"], "value"=> $row["name"] . " " . $row["last_name"]);
            }
            
            $this->response(array('status' => true, 'data' => $data), 200);
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