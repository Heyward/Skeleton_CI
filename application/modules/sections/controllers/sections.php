<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sections extends MY_Controller 
{
	public function index()
	{
		$actions = array();
		$actions["create_section"] = site_url("sections/form");
		
		$data = array();
		$data["title"] = lang("Sections");
		$this->view('list', $data, $actions);
	}
	
	public function setListParameters()
	{
        $this->load->helper('action');

        $model = new Model("Sections", "s", array("id" => "id", "label" => "label", "position" => "position"));
        $model->setNumerics(array("s.id", "s.position"));

        $actions = array();
        array_push($actions, new Action("sections", "form", "edit"));        
        array_push($actions, new Action("sections", "permissions", "permissions"));        
        array_push($actions, new Action("sections", "downPosition", "down_position", false));
        array_push($actions, new Action("sections", "upPosition", "up_position", false));
        array_push($actions, new Action("sections", "delete", "delete", false));        
        
        $this->model   = $model;
        $this->actions = $actions;
	}
    
    public function form ($identifier = 0)
    {
        $id = ($this->input->post("id") > 0) ? $this->input->post("id") : 0;
        $position  = $this->input->post("position");
        $label     = $this->input->post("label");
        
        if ($identifier > 0){
            $output = $this->rest->get('sections/section/', array("id"=>$identifier));
            if ($output->status){
                $section    = $output->data;
                $label      = $section->label;
                $position   = $section->position;
                $id         = $section->id;
            }
        }
        
        $actions = array();
        $actions["return_section"] = site_url("sections/index");
        
        $data = array();
        $data["title"]      = lang("Sections");
        $data["label"]      = $label;
        $data["position"]   = $position;
        $data["id"]         = $id;
        $this->view('form', $data, $actions);
    }
    
    public function persist ()
    {
        $data = array();
        $message = "";
        $error   = "";
        $output  = ""; 
        
        $this->form_validation->set_rules('label', 'lang:section', 'required|callback_existSection['.$this->input->post("id").']');
        $this->form_validation->set_message('existSection', lang('section_exist'));
        $this->form_validation->set_rules('position', 'lang:position', 'required');
        
        if ($this->form_validation->run($this)){
            if ($this->input->post("id") > 0){
                $output = $this->rest->post('sections/section', $this->input->post()); 
            }else{
                $output = $this->rest->put('sections/section', $this->input->post()); 
            }
            
            if (empty($output) == false){
                if ($output->status){
                    $message = ($this->input->post("id") > 0) ? lang('section_edition') : lang('section_creation');
                }else{
                    $error = (isset($output->error)) ? $output->error : "";
                }
            }
            
            if ($this->input->is_ajax_request() == false){
                $this->index();
            }
        }else{
            $error = validation_errors();
            if ($this->input->is_ajax_request() == false){
                $this->form();
            }
        }
        
        if ($this->input->is_ajax_request()){
            $data["message"] = $message;
            $data["error"]   = $error;
            echo json_encode($data);
        }
    }

    public function existSection($label, $id){
        $output = $this->rest->get('sections/exist_section', array("id"=>$id, "label"=>$label)); 
        return $output->exist;
    }
    
    public function delete()
    {
        $data    = array();
        $message = "";
        $warning = "";
        $error   = "";
        
        $output = $this->rest->delete('sections/section', array("id"=>$this->input->post("id")));

        if ($output->status){
            $message = lang("section_delete");
        }else{
            $error = (isset($output->error)) ? $output->error : "";
            $warning = (isset($output->warning)) ? lang($output->warning) : "";
        }
        
        $data["message"] = $message;
        $data["warning"] = $warning;
        $data["error"]   = $error;
        echo json_encode($data);
    }
    
    public function permissions($section = 0){
        if ($section > 0){
            $this->session->set_userdata("section",$section);
        }
        
        $actions = array();
        $actions["add_permission"] = site_url("sections/assignPermissions")."/".$this->session->userdata("section");
        $actions["return_section"] = site_url("sections/index");
        
        
        $data = array();
        $data["title"] = lang("Permissions");
        $this->view('listPermissions', $data, $actions);
    }
    
    public function getListPermissions()
    {
        $this->load->helper('action');
        
        $model = new Model("Permissions", "p", array("id" => "id", "label" => "label", "url" => "url", "in_menu" => "in_menu", "position" => "position"));
        $model->setNumerics(array("p.id", "p.position"));
        
        $section = new Model("Sections", "s");
        $section->setRelation("section");
        $section->setNumerics(array("s.id"));
        
        if ($this->session->userdata("section") > 0){
            $section->setConditions(array("s.id = ".$this->session->userdata("section")));
        }
        
        $relation = array();
        array_push($relation, $section);
        
        $actions = array();
        array_push($actions, new Action("permissions", "downPosition", "down_position", false));
        array_push($actions, new Action("permissions", "upPosition", "up_position", false));
        array_push($actions, new Action("permissions", "changeInMenu", "change_menu", false));
        
        $this->load->helper('permissions');
        $getData = $this->input->get();

        $arrayData  = $this->datatable->getData($getData, $model, $relation);
        $registers  = array();

        foreach ($arrayData['aaData'] as $register) {

            $actionsHtml = "";
            
            foreach ($actions as $aAction){
                if (hasRights($aAction->getMethod(), $aAction->getClass())){
                    $url = $aAction->getUrl();
                    $urlStr = "#";
                    
                    if ($url){
                        $urlStr = site_url($aAction->getClass() . "/" . $aAction->getMethod()). "/" . $register[0];
                    }
                    
                    $trans      = (Soporte::cadenaVacia(lang($aAction->getLabel())) == false) ? lang($aAction->getLabel()) : lang(ucfirst($aAction->getLabel()));
                    $label      = "title='" . $trans . "'";
                    $class      = "class='action_list action_" . $aAction->getLabel() . "'";
                    $id         = "id='".$register[0]."'";
                    $parameters = $label . " " . $class . " " . $id;
                    $actionsHtml  .= Soporte::creaEnlaceTexto("", $urlStr, $parameters);
                }
            }
            
            $register[]  = $actionsHtml;
            $registers[] = $register;
        }

        $arrayData['aaData'] = $registers;
        echo json_encode($arrayData);
    }

    public function assignPermissions ($id)
    {
        $this->loadRepository("Permissions");
        $this->loadRepository("Sections");
        
        $actions = array();
        $actions["return_permission"] = site_url("sections/permissions");
        
        $section = $this->em->find('models\Sections', $id);
        
        $data = array();
        $data["title"]         = lang("assignment_permissions");
        $data["sections"]      = $this->Sections->findAll();
        $data["permissionsSection"] = $section->getPermissions();
        $data["id"]       = $id;
        
        $this->view('assignPermissions', $data, $actions);
    }
    
    public function persistSectionPermission()
    {
        $data    = array();
        $message = "";
        $error   = "";
        $permissions = ($this->input->post('permission')) ? $this->input->post('permission') : array("0");
        $output = $this->rest->put('sections/permissions', array("id"=>$this->input->post('id'), "permissions"=>$permissions));

        if ($output->status){
            $message = $this->lang->line('assignment_permissions_ok');
        }else{
            $error = (isset($output->error)) ? $output->error : "";
        }

        if ($this->input->is_ajax_request() == false){
            redirect("sections/assignPermissions/".$this->input->post('id'));
        }
        
        $data["message"] = $message;
        $data["error"]   = $error;
        echo json_encode($data);
    }
    
    public function upPosition(){
        $this->rest->post('sections/up_position', $this->input->post());
        echo json_encode(array());
    }

    public function downPosition(){
        $this->rest->post('sections/down_position', $this->input->post());
        echo json_encode(array());
    }
}