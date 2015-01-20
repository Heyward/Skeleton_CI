<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logs extends MY_Controller 
{
	public function index()
	{
		$actions = array();
		$actions["create_log"] = site_url("logs/form");
		
		$data = array();
		$data["title"] = lang("Logs");
		$this->view('list', $data, $actions);
	}
	
	public function setListParameters()
	{
        $this->load->helper('action');

        $model = new Model("Logs", "l", array("id" => "id", 'date' => 'date' ,'time' => 'time' ,'logType' => 'logType' ,'data' => 'data' ,'oldData' => 'oldData' ,'origin' => 'origin' ,'user' => 'user'));
        $model->setNumerics(array("l.id"));

        $actions = array();
        array_push($actions, new Action("logs", "form", "edit"));        
        array_push($actions, new Action("logs", "delete", "delete", false));        
        
        $this->model   = $model;
        $this->actions = $actions;
	}
    
    public function form ($identifier = 0)
    {
        $id = ($this->input->post("id") > 0) ? $this->input->post("id") : 0;
        $date = $this->input->post('date');
		$time = $this->input->post('time');
		$logType = $this->input->post('logType');
		$data = $this->input->post('data');
		$oldData = $this->input->post('oldData');
		$origin = $this->input->post('origin');
		$user = $this->input->post('user');
        
        if ($identifier > 0){
            $output = $this->rest->get('logs/log/', array("id"=>$identifier));
        
            if ($output->status){
                $log    = $output->data;
                $date = $log->date;
				$time = $log->time;
				$logType = $log->logType;
				$data = $log->data;
				$oldData = $log->oldData;
				$origin = $log->origin;
				$user = $log->user;
                $id         = $log->id;
            }
        }
        
        $actions = array();
        $actions["return_log"] = site_url("logs/index");
        
        $data = array();
        $data["title"]  = lang("Logs");
        $data['date'] = $date;
		$data['time'] = $time;
		$data['logType'] = $logType;
		$data['data'] = $data;
		$data['oldData'] = $oldData;
		$data['origin'] = $origin;
		$data['user'] = $user;
        $data["id"] = $id;
        $this->view('form', $data, $actions);
    }
    
    public function persist ()
    {
        $data = array();
        $message = "";
        $error   = "";
        $output  = ""; 
        
        $this->form_validation->set_rules('date', 'lang:date', 'required');
		$this->form_validation->set_rules('time', 'lang:time', 'required');
		$this->form_validation->set_rules('logType', 'lang:logType', 'required');
		$this->form_validation->set_rules('data', 'lang:data', 'required');
		$this->form_validation->set_rules('oldData', 'lang:oldData', 'required');
		$this->form_validation->set_rules('origin', 'lang:origin', 'required');
		$this->form_validation->set_rules('user', 'lang:user', 'required');
                
        if ($this->form_validation->run($this)){
            if ($this->input->post("id") > 0){
                $output = $this->rest->post('logs/log', $this->input->post()); 
            }else{
                $output = $this->rest->put('logs/log', $this->input->post()); 
            }
            
            if (empty($output) == false){
                if ($output->status){
                    $message = ($this->input->post("id") > 0) ? lang('log_edition') : lang('log_creation');
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
        
        $output = $this->rest->delete('logs/log', array("id"=>$this->input->post("id")));

        if ($output->status){
            $message = lang("log_delete");
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