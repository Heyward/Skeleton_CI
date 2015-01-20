<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class Profiles extends MY_Controller
{
    public function index ()
    {
        $actions = array();
        $actions["create_profile"] = site_url("profiles/form");
        
        $data = array();
        $data["title"] = lang("Profiles");
        $this->view('list', $data, $actions);
    }
    
    public function setListParameters ()
    {
        $this->load->helper('action');
        
        $model = new Model("Profiles", "p", array("id" => "id", "name" => "name", "description" => "description"));
        $model->setNumerics(array("p.id"));
        
        $actions = array();
        array_push($actions, new Action("profiles", "form", "edit"));
        array_push($actions, new Action("profiles", "assignPermissions", "permissions"));
        array_push($actions, new Action("profiles", "delete", "delete", false));
        
        $this->model = $model;
        $this->actions = $actions;
    }
    
    public function form ($identifier = 0)
    {
        $id             = ($this->input->post("id") > 0) ? $this->input->post("id") : 0;
        $name           = $this->input->post("name");
        $description    = $this->input->post("description");
        
        if ($identifier > 0) {
            $output = $this->rest->get('profiles/profile/', array("id"=>$identifier));
        
            if ($output->status){
                $profile        = $output->data;
                $name           = $profile->name;
                $description    = $profile->description;
                $id             = $profile->id;
            } 
        }
        
        $actions = array();
        $actions["return_profile"]  = site_url("profiles/index");
        
        $data = array();
        $data["title"]          = lang("Profiles");
        $data["name"]           = $name;
        $data["description"]    = $description;
        $data["id"]             = $id;
        $this->view('form', $data, $actions);
    }
    
    public function persist ()
    {
        $this->loadRepository("Profiles");
        $data = array();
        $message = "";
        $error   = "";
        $output  = ""; 
        
        $this->form_validation->set_rules('name', 'lang:name', 'required|callback_existProfile['.$this->input->post("id").']');
        $this->form_validation->set_message('existProfile', lang('profile_exist'));
        $this->form_validation->set_rules('description', 'lang:description', 'required');
        
        if ($this->form_validation->run($this)){
            if ($this->input->post("id") > 0){
                $output = $this->rest->post('profiles/profile', $this->input->post()); 
            }else{
                $output = $this->rest->put('profiles/profile', $this->input->post()); 
            }
            
            if (empty($output) == false){
                if ($output->status){
                    $message = ($this->input->post("id") > 0) ? lang('profile_edition') : lang('profile_creation');
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

    public function existProfile($name, $id){
        $output = $this->rest->get('profiles/exist_profile', array("id"=>$id, "name"=>$name)); 
        return $output->exist;
    }

    public function delete ()
    {
        $data    = array();
        $message = "";
        $warning = "";
        $error   = "";
        
        $output = $this->rest->delete('profiles/profile', array("id"=>$this->input->post("id")));
        
        if ($output->status){
            $message = lang("profile_delete");
        }else{
            $error = (isset($output->error)) ? $output->error : "";
            $warning = (isset($output->warning)) ? lang($output->warning) : "";
        }
        
        $data["message"] = $message;
        $data["warning"] = $warning;
        $data["error"]   = $error;
        echo json_encode($data);
    }
    
    public function assignPermissions ($id)
    {
        $this->loadRepository("Permissions");
        $this->loadRepository("Sections");
        
        $actions = array();
        $actions["return_profile"] = site_url("profiles/index");
        
        $profile = $this->em->find('models\Profiles', $id);
        
        $data = array();
        $data["title"]         = lang("assignment_permissions");
        $data["sections"]      = $this->Sections->findAll();
        $data["permissions"]       = $this->Permissions->findAll();
        $data["permissionsProfile"] = $profile->getPermissions();
        $data["id"]       = $id;
        
        $this->view('assignPermissions', $data, $actions);
    }
    
    public function persistProfilePermission()
    {
        $data    = array();
        $message = "";
        $error   = "";
        $permissions = ($this->input->post('permission')) ? $this->input->post('permission') : array("0");
        $output = $this->rest->put('profiles/permissions', array("id"=>$this->input->post('id'), "permissions"=>$permissions));

        if ($output->status){
            $message = $this->lang->line('assignment_permissions_ok');
        }else{
            $error = (isset($output->error)) ? $output->error : "";
        }
        
        if ($this->input->is_ajax_request() == false){
            redirect("profiles/assignPermissions/".$this->input->post('id'));
        }
        
        $data["message"] = $message;
        $data["error"]   = $error;
        echo json_encode($data);
    }
}