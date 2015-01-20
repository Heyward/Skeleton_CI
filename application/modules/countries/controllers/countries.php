<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Countries extends MY_Controller 
{
	public function index()
	{
		$actions = array();
		$actions["create_country"] = site_url("countries/form");
		
		$data = array();
		$data["title"] = lang("Countries");
		$this->view('list', $data, $actions);
	}
	
	public function setListParameters()
	{
        $this->load->helper('action');

        $model = new Model("Countries", "c", array("id" => "id", "name" => "name", "code" => "code"));
        $model->setNumerics(array("c.id"));

        $actions = array();
        array_push($actions, new Action("countries", "form", "edit"));        
        array_push($actions, new Action("cities", "index", "cities"));        
        array_push($actions, new Action("countries", "delete", "delete", false));        
        
        $this->model   = $model;
        $this->actions = $actions;
	}
    
    public function form ($identifier = 0)
    {
        $id = ($this->input->post("id") > 0) ? $this->input->post("id") : 0;
        $name = $this->input->post("name");
        $code = $this->input->post("code");
        
        if ($identifier > 0){
            $output = $this->rest->get('countries/country/', array("id"=>$identifier));
        
            if ($output->status){
                $country    = $output->data;
                $name       = $country->name;
                $code       = $country->code;
                $id         = $country->id;
            }
        }
        
        $actions = array();
        $actions["return_country"] = site_url("countries/index");
        
        $data = array();
        $data["title"]  = lang("Countries");
        $data["name"]   = $name;
        $data["code"]   = $code;
        $data["id"] = $id;
        
        $this->view('form', $data, $actions);
    }

    public function persist ()
    {
        $data = array();
        $message = "";
        $error   = "";
        $output  = ""; 
        
        $this->form_validation->set_rules('name', 'lang:name', 'required|callback_existCountry['.$this->input->post("id").']');
        $this->form_validation->set_rules('code', 'lang:code', 'required|callback_existCode['.$this->input->post("id").']');
        $this->form_validation->set_message('existCountry', lang('country_exist'));
        $this->form_validation->set_message('existCode', lang('country_exist_code'));
        
        if ($this->form_validation->run($this)){
            if ($this->input->post("id") > 0){
                $output = $this->rest->post('countries/country', $this->input->post()); 
            }else{
                $output = $this->rest->put('countries/country', $this->input->post()); 
            }
            
            if (empty($output) == false){
                if ($output->status){
                    $message = ($this->input->post("id") > 0) ? lang('country_edition') : lang('country_creation');
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

    public function existCountry($name, $id){
        $output = $this->rest->get('countries/exist_country', array("id"=>$id, "name"=>$name)); 
        return $output->exist;
    }

    public function existCode($code, $id){
        $output = $this->rest->get('countries/exist_code', array("id"=>$id, "code"=>$code)); 
        return $output->exist;
    }
    
    public function delete()
    {
        $data    = array();
        $message = "";
        $warning = "";
        $error   = "";
        
        $output = $this->rest->delete('countries/country', array("id"=>$this->input->post("id")));

        if ($output->status){
            $message = lang("country_delete");
        }else{
            $error = (isset($output->error)) ? $output->error : "";
            $warning = (isset($output->warning)) ? lang($output->warning) : "";
        }
        
        $data["message"] = $message;
        $data["warning"] = $warning;
        $data["error"]   = $error;
        echo json_encode($data);
    }
}