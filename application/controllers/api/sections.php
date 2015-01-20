<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Sections extends REST_Controller
{
    function section_get()
    {
        try{
            if(!$this->get('id')){
                $this->response(NULL, 400);
            }
            
            $section = $this->em->find('models\Sections', $this->get('id'));
            
            if($section){
                $this->response(array("status"=>true, "data"=>$section->toArray()), 200);
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
    
    function section_post()
    {
        try {
            if (!$this->post("id")){
                $this->response(NULL, 400);
            }
        
            $section = $this->em->find('models\Sections', $this->post("id"));
            $section->setLabel($this->post("label"));
            $section->setPosition($this->post("position"));
            $this->em->persist($section);
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

    function section_put()
    {
        try {
            $section = new models\Sections();
            $section->setLabel($this->put("label"));
            $section->setPosition($this->put("position"));
            $this->em->persist($section);
            $this->em->flush();
            $id = $section->getId();
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

    function section_delete()
    {
        try {
            if (!$this->delete("id")){
                $this->response(NULL, 400);
            }
            
            $this->loadRepository("Permissions");
            
            $idSection = $this->delete("id");
            $section        = $this->em->find('models\Sections', $idSection);
            $permissions = $this->Permissions->findBy(array("section"=>$idSection));
            $havePermissions  = (count($permissions) > 0);
            
            if ($havePermissions) {
                $this->response(array('status' => false, 'warning' => "section_have_permissions"), 400);
            }
            
            if ($havePermissions == false) {
                    $this->em->remove($section);
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
    
    function exist_section_get()
    {
        try{
            if(!is_numeric($this->get('id')) || !$this->get('label')){
                $this->response(NULL, 400);
            }
            
            $this->loadRepository("Sections");
            $section = new models\Sections();
            
            if ($this->get('id') > 0) {
                $section = $this->em->find('models\Sections', $this->get('id'));
            }
    
            $label      = $this->get('label');
            $edition    = ($this->get('id')  > 0);
            $different  = ($label != $section->getLabel());
            $sectionAux = $this->Sections->findOneBy(array("label" => $label));
            $exist      = (empty($sectionAux) == false);
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
        
            $this->loadRepository("Permissions");
            $permissionsPost= $this->put('permissions');
            $section        = $this->em->find('models\Sections', $this->put('id'));
            $permissions    = $this->Permissions->findBy(array("section"=>$section->getId()), array("position"=>"desc"));
            $lastPermission = $permissions[0];
            $lastPosition = ($lastPermission) ? $lastPermission->getPosition() : 0;
            
            foreach ($permissionsPost as $aPermission) {
                if ($aPermission > 0){
                    $permission = $this->em->find('models\Permissions', $aPermission);
                    if ($permission->getSection()->getId() != $section->getId()){
                        $permission->setSection($section);
                        $permission->setPosition($lastPosition + 1);
                        $this->em->persist($permission);
                    }
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

    function up_position_post()
    {
        try {
            if (!$this->post("id")){
                $this->response(NULL, 400);
            }
        
            $this->loadRepository("Sections");
            $section = $this->em->find('models\Sections', $this->post("id"));
            $position = $section->getPosition();
            $sectionAux = $this->Sections->findOneBy(array("position"=>$position+1), array("position" => "asc"));
            $sections = $this->Sections->findBy(array(), array("position" => "desc"));
            $firstSection = $sections[0];
            
            if ($position + 1 <= count($sections)){
                $section->setPosition($position + 1);
                $this->em->persist($section);
                
                if (!empty($sectionAux)){
                    $sectionAux->setPosition($position);
                    $this->em->persist($sectionAux);
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
        
            $this->loadRepository("Sections");
            $section = $this->em->find('models\Sections', $this->post("id"));
            $position = $section->getPosition();
            
            if ($position - 1 > 0 ){
                $sectionAux = $this->Sections->findOneBy(array("position"=>$position-1), array("position" => "asc"));
                
                $section->setPosition($position - 1);
                $this->em->persist($section);
                
                if (!empty($sectionAux)){
                    $sectionAux->setPosition($position);
                    $this->em->persist($sectionAux);
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