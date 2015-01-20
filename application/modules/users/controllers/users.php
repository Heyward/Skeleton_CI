<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends MY_Controller 
{
	public function index()
	{
		$actions = array();
		$actions["create_user"] = site_url("users/form");
		
		$data = array();
		$data["title"] = lang("Users");
		$this->view('list', $data, $actions);
	}
	
	public function setListParameters()
	{
        $this->load->helper('action');
        
        $model = new Model("Users", "u", array("id"=>"id", "name"=>"name", "last_name"=>"last_name", "email"=>"email"));
        $model->setNumerics(array("u.id"));
        
        $profile = new Model("Profiles", "p", array("name"=>"nameP"));
        $profile->setRelation("profile");
        $profile->setConditions(array("p.id<>".AuthConstants::ID_PROFILE_USER));
        
        $city = new Model("Cities", "c", array("name"=>"nameC"));
        $city->setRelation("city");
        
        $country = new Model("Countries", "co", array("name"=>"nameCo"));
        $country->setRelation("country");
        $country->setModelJoin("c");
        
        $relations = array();
        array_push($relations, $profile);
        array_push($relations, $city);
        array_push($relations, $country);
        
        $actions = array();
        array_push($actions, new Action("users", "form", "edit"));        
        array_push($actions, new Action("users", "delete", "delete", false));        
        
        $this->model        = $model;
        $this->actions      = $actions;
        $this->relations    = $relations;
	}
    
    public function form ($identifier = 0)
    {
        $this->loadRepository("Profiles");
        $this->loadRepository("Countries");
        $this->loadRepository("Cities");
        
        $id         = ($this->input->post("id") > 0) ? $this->input->post("id") : 0;
        $idProfile  = $this->input->post("idProfile");
        $name       = $this->input->post("name");
        $lastName   = $this->input->post("lastName");
        $email      = $this->input->post("email");
        $language   = $this->input->post("language");
        $idCity     = $this->input->post("idCity");
        $idCountry  = $this->input->post("idCountry");
        
        if ($identifier > 0){
            $output = $this->rest->get('users/user/', array("id"=>$identifier));

            if ($output->status){
                $user       = $output->data;
                $name       = $user->name;
                $lastName   = $user->last_name;
                $email      = $user->email;
                $language   = $user->language;
                $idProfile  = $user->profile->id;
                $idCity     = $user->city->id;
                $idCountry  = $user->city->country->id;
                $id         = $user->id;
            }
        }

        $actions = array();
        $actions["return_user"] = site_url("users/index");
        
        $cities = array();
        
        if (is_numeric($idCountry) && $idCountry > 0){
            $cities = $this->Cities->findBy(array("country"=>$idCountry));
        }
        
        
        $data = array();
        $data["title"]      = lang("Users");
        $data["name"]       = $name;
        $data["last_name"]  = $lastName;
        $data["email"]      = $email;
        $data["language"]   = $language;
        $data["languages"]  = $this->config->config['languages'];
        $data["idProfile"]  = $idProfile;
        $data["id"]         = $id;
        $data["profiles"]   = $this->Profiles->findAll();
        $data["idCountry"]  = $idCountry;
        $data["countries"]  = $this->Countries->findAll();
        $data["cities"]     = $cities;
        $data["idCity"]     = $idCity;
        $this->view('form', $data, $actions);
    }
    
    public function persist ()
    {
        $this->loadRepository("Users");
        $data = array();
        $message = "";
        $error   = "";
        $output  = ""; 
        
        $this->form_validation->set_rules('idCity', 'lang:city', 'required');
        $this->form_validation->set_rules('idProfile', 'lang:profile', 'required');
        $this->form_validation->set_rules('name', 'lang:name', 'required');
        $this->form_validation->set_rules('language', 'lang:language', 'required');
        $this->form_validation->set_rules('lastName', 'lang:last_name', 'required');
        $this->form_validation->set_rules('email', 'lang:email', 'required|callback_existUser['.$this->input->post("id").']');
        $this->form_validation->set_message('existUser', lang('user_exist'));
        
        if ($this->form_validation->run($this)){
            if ($this->input->post("id") > 0){
                $output = $this->rest->post('users/user', $this->input->post()); 
            }else{
                $output = $this->rest->put('users/user', $this->input->post()); 
            }
            
            if (empty($output) == false){
                if ($output->status){
                    $message = ($this->input->post("id") > 0) ? lang('user_edition') : lang('user_creation');
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
    
    public function existUser($email, $id)
    {
        $output = $this->rest->get('users/exist_user', array("id"=>$id, "email"=>$email)); 
        return $output->exist;
    }
    
    public function delete ()
    {
        $data    = array();
        $message = "";
        $warning = "";
        $error   = "";
        
        $output = $this->rest->delete('users/user', array("id"=>$this->input->post("id")));
        
        if ($output->status){
            $message = lang("user_delete");
        }else{
            $error = (isset($output->error)) ? $output->error : "";
            $warning = (isset($output->warning)) ? lang($output->warning) : "";
        }
        
        $data["message"] = $message;
        $data["warning"] = $warning;
        $data["error"]   = $error;
        echo json_encode($data);
    }
    
    public function myData()
    {
        $this->loadRepository("Cities");
        
        $name       = "";
        $lastName   = "";
        $email      = "";
        $language   = "";
        $idCity     = "";
        
        $user = null;
        $output = $this->rest->get('users/user/', array("id"=>$this->session->userdata(AuthConstants::USER_ID)));

        if ($output->status){
            $user       = $output->data;
            $name       = $user->name;
            $lastName   = $user->last_name;
            $email      = $user->email;
            $language   = $user->language;
            $idCity     = $user->city->id;
        }

        if (is_array($this->input->post()) && count($this->input->post()) > 0){
            $name       = $this->input->post("name");
            $lastName   = $this->input->post("lastName");
            $email      = $this->input->post("email");
            $language   = $this->input->post("language");
            $idCity     = $this->input->post("idCity");
        }
        
        $dataMyData = array();
        $dataMyData["title"]      = lang("personal_data");
        $dataMyData["titleSection"] = lang("my_profile");
        $dataMyData["name"]       = $name;
        $dataMyData["last_name"]  = $lastName;
        $dataMyData["email"]      = $email;
        $dataMyData["language"]   = $language;
        $dataMyData["languages"]  = $this->config->config["languages"];
        $dataMyData["id"]         = $this->session->userdata(AuthConstants::USER_ID);
        $dataMyData["cities"]     = $this->Cities->findBy(array("country"=>$user->city->country->id));
        $dataMyData["idCity"]     = $idCity;
        $dataMyData["span"]  = 6;
        
        
        $dataPassword = array();
        $dataPassword["title"] = lang("change_password");
        $dataPassword["span"]  = 6;
        $dataPassword["id"]    = $this->session->userdata(AuthConstants::USER_ID);
        
        $views = array(); 
        $views['form_mydata'] = $dataMyData;
        $views['form_password'] = $dataPassword;
                
        $this->viewSections($views);
    }

    public function persistMyData ()
    {
        $data = array();
        $message = "";
        $error   = "";
        $output  = "";
        
        $this->form_validation->set_rules('idCity', 'lang:city', 'required');
        $this->form_validation->set_rules('name', 'lang:name', 'required');
        $this->form_validation->set_rules('lastName', 'lang:last_name', 'required');
        $this->form_validation->set_rules('language', 'lang:language', 'required');
        $this->form_validation->set_rules('email', 'lang:email', 'required|callback_existUser['.$this->input->post("id").']');
        $this->form_validation->set_message('existUser', lang('user_exist'));
        
        if ($this->form_validation->run($this)){
            $output = $this->rest->post('users/mydata', $this->input->post()); 
            
            if (empty($output) == false){
                if ($output->status){
                    $message = lang('mydata_success');
                    $this->session->set_userdata(AuthConstants::LANG,       $this->input->post("language"));
                    $this->session->set_userdata(AuthConstants::EMAIL,      $this->input->post("email"));
                    $this->session->set_userdata(AuthConstants::NAMES,      $this->input->post("name"));
                    $this->session->set_userdata(AuthConstants::LAST_NAMES, $this->input->post("lastName"));
                }else{
                    $error = (isset($output->error)) ? $output->error : "";
                }
            }
            
            if ($this->input->is_ajax_request() == false){
                $this->myData();
            }
        }else{
            $error = validation_errors();
            if ($this->input->is_ajax_request() == false){
                $this->myData();
            }
        }
        
        if ($this->input->is_ajax_request()){
            $data["message"] = $message;
            $data["error"]   = $error;
            echo json_encode($data);
        }
    }   

    public function persistPassword ()
    {
        $data = array();
        $message = "";
        $error   = "";
        $output  = "";
        
        $this->form_validation->set_rules('password', 'lang:password', 'required|callback_verifyPassword');
        $this->form_validation->set_rules('new_password', 'lang:new_password', 'required');
        $this->form_validation->set_rules('re_password', 'lang:re_password', 'required');
        $this->form_validation->set_message('verifyPassword', lang('wrong_password'));
        
        if ($this->form_validation->run($this)){
            $output = $this->rest->post('users/change_password', $this->input->post()); 
            
            if (empty($output) == false){
                if ($output->status){
                    $message = lang('password_success');
                }else{
                    $error = (isset($output->error)) ? $output->error : "";
                }
            }
            
            if ($this->input->is_ajax_request() == false){
                $this->myData();
            }
        }else{
            $error = validation_errors();
            if ($this->input->is_ajax_request() == false){
                $this->myData();
            }
        }
        
        if ($this->input->is_ajax_request()){
            $data["message"] = $message;
            $data["error"]   = $error;
            echo json_encode($data);
        }
    }

    public function verifyPassword($password)
    {
        $output = $this->rest->get('users/verify_password', array("id"=>$this->session->userdata(AuthConstants::USER_ID), "password"=>$password)); 
        return $output->exist;
    } 
    
    public function getUsersAutocomplete()
    {
        $data = array();
        $data["term"] = $this->input->get("term");
        $data["country"] = 0;
        
        if ($this->session->userdata(AuthConstants::ADMIN) == AuthConstants::ADMIN_OK){
            $data["country"] = $this->session->userdata(AuthConstants::COUNTRY);
        } 
        
        $output = $this->rest->get('users/autocomplete', $data); 
        echo json_encode($output->data);
    }
    
    public function getUsersByWorkshop(){
        $output = $this->rest->get('users/list_by_workshop', $this->input->post()); 
        echo json_encode($output);
    }
}