<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cities extends MY_Controller 
{
	public function index($country = 0)
	{
		$actions = array();
		$actions["create_city"] = site_url("cities/form");
        
        $this->session->set_userdata("country",$country);
        
        if ($this->session->userdata("country") > 0){
    		$actions["return_country"] = site_url("countries/index");
        }
		
		$data = array();
		$data["title"] = lang("Cities");
		$this->view('list', $data, $actions);
	}
	
	public function setListParameters()
	{
        $this->load->helper('action');

        $model = new Model("Cities", "c", array("id" => "id", "name" => "name"));
        $model->setNumerics(array("c.id"));
        
        $country = new Model("Countries", "co", array("name"=>"nameC"));
        $country->setRelation("country");
        
        if ($this->session->userdata("country") > 0){
            $country->setConditions(array("co.id = ".$this->session->userdata("country")));
        }
        
        $relations = array();
        array_push($relations, $country);
        
        $actions = array();
        array_push($actions, new Action("cities", "form", "edit"));        
        array_push($actions, new Action("cities", "delete", "delete", false));        
        
        $this->model = $model;
        $this->actions = $actions;
        $this->relations = $relations;
	}
    
    public function form ($identifier = 0)
    {
        $this->loadRepository("Countries");
        
        $id        = ($this->input->post("id") > 0) ? $this->input->post("id") : 0;
        $name      = $this->input->post("name");
        $idCountry = $this->input->post("idCountry");
        
        if ($identifier > 0){
            $output = $this->rest->get('cities/city/', array("id"=>$identifier));
        
            if ($output->status){
                $city       = $output->data;
                $name       = $city->name;
                $idCountry  = $city->country->id;
                $id         = $city->id;
            }
        }
        
        $actions= array();
        $actions["return_city"] = site_url("cities/index")."/".$this->session->userdata("country");
        
        $data = array();
        $data["title"]  = lang("Cities");
        $data["name"]  = $name;
        $data["id"]  = $id;
        $data["idCountry"]  = ($this->session->userdata("country") > 0) ? $this->session->userdata("country") : $idCountry;
        $data["countries"]  = $this->Countries->findAll();
        $this->view('form', $data, $actions);
    }
    
    public function persist ()
    {
        $data = array();
        $message = "";
        $error   = "";
        $output  = ""; 
        
        $this->form_validation->set_rules('name', 'lang:name', 'required');
        $this->form_validation->set_rules('idCountry', 'lang:country', 'required');
                
        if ($this->form_validation->run($this)){
            if ($this->input->post("id") > 0){
                $output = $this->rest->post('cities/city', $this->input->post()); 
            }else{
                $output = $this->rest->put('cities/city', $this->input->post()); 
            }
            if (empty($output) == false){
                if ($output->status){
                    $message = ($this->input->post("id") > 0) ? lang('city_edition') : lang('city_creation');
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

    public function delete()
    {
        $data    = array();
        $message = "";
        $warning = "";
        $error   = "";
        
        $output = $this->rest->delete('cities/city', array("id"=>$this->input->post("id")));

        if ($output->status){
            $message = lang("city_delete");
        }else{
            $error = (isset($output->error)) ? $output->error : "";
            $warning = (isset($output->warning)) ? lang($output->warning) : "";
        }
        
        $data["message"] = $message;
        $data["warning"] = $warning;
        $data["error"]   = $error;
        echo json_encode($data);
    }
    
    public function getCitiesByCountry(){
        $output = $this->rest->get('cities/list', array("country"=>$this->input->post("idCountry")));
        echo json_encode($output);
    }
    
    public function getCitiesByUser(){
        $output = $this->rest->get('cities/list_by_user', array("user"=>$this->input->post("id")));
        echo json_encode($output);
    }
}