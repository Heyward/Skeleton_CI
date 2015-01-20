<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Cities extends REST_Controller
{
	function city_get()
    {
        try{
            if(!$this->get('id')){
            	$this->response(NULL, 400);
            }
            
            $city = $this->em->find('models\Cities', $this->get('id'));
            
            if($city){
                $this->response(array("status"=>true, "data"=>$city->toArray()), 200);
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

	function list_get()
    {
        try{
            $this->loadRepository("Cities");
            
            $cities = $this->Cities->findAll();
            
            if(is_numeric($this->get('country'))){
                $cities = $this->Cities->findBy(array("country"=>$this->get("country")));
            }
            
            if($cities){
                $return = array();
                
                foreach($cities as $aCity){
                    $return[$aCity->getId()] = $aCity->toArray(); 
                }
                
                $this->response(array("status"=>true, "data"=>$return, 200));
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
    

	function list_by_user_get()
    {
        try{
            $this->loadRepository("Cities");
            
            $cities = $this->Cities->findAll();
            
            $country = "";
            if(is_numeric($this->get('user'))){
                $user = $this->em->find('models\Users', $this->get('user'));
                $cities = $this->Cities->findBy(array("country"=>$user->getCity()->getCountry()->getId()));
                $country = $user->getCity()->getCountry()->toArray();
            }
            
            if($cities){
                $return = array();
                
                foreach($cities as $aCity){
                    $return[$aCity->getId()] = $aCity->toArray(); 
                }
                
                $this->response(array("status"=>true, "data"=>$return, "country"=>$country, 200));
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
    
    function city_post()
    {
        try {
            if (!$this->post("id")){
            	$this->response(NULL, 400);
            }
        
            $country = $this->em->find('models\Countries', $this->post("idCountry"));
            
            $city = $this->em->find('models\Cities', $this->post("id"));
            $city->setName($this->post("name"));
            $city->setCountry($country);
            
            $this->em->persist($city);
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

    function city_put()
    {
        try {
            $country = $this->em->find('models\Countries', $this->put("idCountry"));
            
            $city = new models\Cities();
            $city->setName($this->put("name"));
            $city->setCountry($country);
            
            $this->em->persist($city);
            $this->em->flush();
            
            $id = $city->getId();
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

    function city_delete()
    {
        try {
            if (!$this->delete("id")){
            	$this->response(NULL, 400);
            }
            
            $this->loadRepository("Users");
            
            $id         = $this->delete("id");
            $city       = $this->em->find('models\Cities', $id);
            $users      = $this->Users->findBy(array("city" => $id));
            $haveUsers  = (count($users) > 0);
            
            if ($haveUsers) {
                $this->response(array('status' => false, 'warning' => "city_have_users"), 400);
            }
            
            if ($haveUsers == false) {
                    $this->em->remove($city);
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
}