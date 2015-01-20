<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Countries extends REST_Controller
{
	function country_get()
    {
        try{
            if(!$this->get('id')){
            	$this->response(NULL, 400);
            }
            
            $country = $this->em->find('models\Countries', $this->get('id'));
            
            if ($country){
                $this->response(array("status"=>true, "data"=>$country->toArray()), 200);
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
    
    function country_post()
    {
        try {
            if (!$this->post("id")){
            	$this->response(NULL, 400);
            }
        
            $country = $this->em->find('models\Countries', $this->post("id"));
            $country->setName($this->post("name"));
            $country->setCode($this->post("code"));
            $this->em->persist($country);
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

    function country_put()
    {
        try {
            $country = new models\Countries();
            $country->setName($this->put("name"));
            $country->setCode($this->put("code"));
            $this->em->persist($country);
            $this->em->flush();
            $id = $country->getId();
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

    function country_delete()
    {
        try {
            if (!$this->delete("id")){
            	$this->response(NULL, 400);
            }
            
            $this->loadRepository("Cities");
            
            $id         = $this->delete("id");
            $country    = $this->em->find('models\Countries', $id);
            $cities     = $this->Cities->findBy(array("country"=>$id));
            $haveCities = (count($cities) > 0);
            
            if ($haveCities) {
                $this->response(array('status' => false, 'warning' => "country_have_cities"), 400);
            }
            
            if ($haveCities == false) {
                    $this->em->remove($country);
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
    
    function exist_country_get()
    {
        try{
            if(!is_numeric($this->get('id')) || !$this->get('name')){
                $this->response(NULL, 400);
            }
            
            $this->loadRepository("Countries");
            $country = new models\Countries();
            
            if ($this->get('id') > 0) {
                $country = $this->em->find('models\Countries', $this->get('id'));
            }
    
            $name       = $this->get('name');
            $edition    = ($this->get('id')  > 0);
            $different  = ($name != $country->getName());
            $countryAux = $this->Countries->findOneBy(array("name" => $name));
            $exist      = (empty($countryAux) == false);
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

    function exist_code_get()
    {
        try{
            if(!is_numeric($this->get('id')) || !$this->get('code')){
                $this->response(NULL, 400);
            }
            
            $this->loadRepository("Countries");
            $country = new models\Countries();
            
            if ($this->get('id') > 0) {
                $country = $this->em->find('models\Countries', $this->get('id'));
            }
    
            $code       = $this->get('code');
            $edition    = ($this->get('id')  > 0);
            $different  = ($code != $country->getCode());
            $countryAux = $this->Countries->findOneBy(array("code" => $code));
            $exist      = (empty($countryAux) == false);
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