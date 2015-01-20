<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Logs extends REST_Controller
{
	function log_get()
    {
        try{
            if(!$this->get('id')){
            	$this->response(NULL, 400);
            }
            
            $log = $this->em->find('models\Logs', $this->get('id'));
            
            if ($log){
                $this->response(array("status"=>true, "data"=>$log->toArray()), 200);
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
    
    function log_post()
    {
        try {
            if (!$this->post("id")){
            	$this->response(NULL, 400);
            }
        
            $log = $this->em->find('models\Logs', $this->post("id"));
            $log->setDate($this->post('date'));
			$log->setTime($this->post('time'));
			$log->setLogType($this->post('logType'));
			$log->setData($this->post('data'));
			$log->setOldData($this->post('oldData'));
			$log->setOrigin($this->post('origin'));
			$log->setUser($this->post('user'));
            $this->em->persist($log);
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

    function log_put()
    {
        try {
            $log = new models\Logs();
            $log->setDate($this->put('date'));
			$log->setTime($this->put('time'));
			$log->setLogType($this->put('logType'));
			$log->setData($this->put('data'));
			$log->setOldData($this->put('oldData'));
			$log->setOrigin($this->put('origin'));
			$log->setUser($this->put('user'));
            $this->em->persist($log);
            $this->em->flush();
            $id = $log->getId();
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

    function log_delete()
    {
        try {
            if (!$this->delete("id")){
            	$this->response(NULL, 400);
            }
            
            $id           = $this->delete("id");
            $log = $this->em->find('models\Logs', $id);
        
            $this->em->remove($log);
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
}