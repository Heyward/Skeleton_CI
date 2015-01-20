<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class Permissions extends MY_Controller
{
    public function index ($section = 0)
    {
        $data = array();
        $data["title"] = lang("Permissions");
        $this->view('list', $data);
    }
    
    public function setListParameters ()
    {
        $this->load->helper('action');
        
        $model = new Model("Permissions", "p", array("id" => "id", "label" => "label", "url" => "url", "in_menu" => "in_menu", "position" => "position"));
        $model->setNumerics(array("p.id", "p.position"));
        
        $section = new Model("Sections", "s", array("label" => "label_section"));
        $section->setRelation("section");
        $section->setNumerics(array("s.id"));
        
        $relation = array();
        array_push($relation, $section);
        
        $actions = array();
        array_push($actions, new Action("permissions", "form", "edit"));
        array_push($actions, new Action("permissions", "changeInMenu", "change_menu", false));
        
        $this->model = $model;
        $this->relations = $relation;
        $this->actions = $actions;
    }
    
    public function form ($identifier = 0)
    {
        $this->loadRepository("Sections");
        
        $inMenu = "";
        $position = 0;
        $id = "";
        $idSection = "";
        
        if ($identifier > 0) {
            $output = $this->rest->get('permissions/permission/', array("id"=>$identifier));
            
            if ($output->status){
                $permission = $output->data;
                $inMenu     = $permission->in_menu;
                $position   = $permission->position;
                $idSection  = $permission->section->id;
                $id         = $permission->id;
            }
        }
        
        $actions = array();
        $actions["return_permission"] = site_url("permissions/index");
        
        $data = array();
        $data["title"]      = lang("Permissions");
        $data["inMenu"]     = $inMenu;
        $data["position"]   = $position;
        $data["id"]         = $id;
        $data["idSection"]  = $idSection;
        $data["sections"]   = $this->Sections->findAll();
        $this->view('form', $data, $actions);
    }
    
    public function persist ()
    {
        $this->loadRepository("Permissions");
        $data = array();
        $message = "";
        $error   = "";
        $output  = "";
        
        $this->form_validation->set_rules('idSection', 'lang:section', 'required');
        $this->form_validation->set_rules('position', 'lang:position', 'required');
        
        if ($this->form_validation->run($this)){
            if ($this->input->post("id") > 0) {
                $output = $this->rest->post('permissions/permission', $this->input->post());
            }
            
            if (empty($output) == false){
                if ($output->status){
                    $message = ($this->input->post("id") > 0) ? lang('permission_edition') : lang('permission_creation');
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
                $this->alta();
            }
        }
        
        if ($this->input->is_ajax_request()){
            $data["message"] = $message;
            $data["error"]   = $error;
            echo json_encode($data);
        }
    }
    
    public function changeInMenu ()
    {
        $this->rest->post('permissions/change_inmenu', $this->input->post());
        echo json_encode(array());
    }
    
    public function upPosition(){
        $this->rest->post('permissions/up_position', $this->input->post());
        echo json_encode(array());
    }

    public function downPosition(){
        $this->rest->post('permissions/down_position', $this->input->post());
        echo json_encode(array());
    }
}