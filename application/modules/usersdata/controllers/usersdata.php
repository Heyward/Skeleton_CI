<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UsersData extends MY_Controller 
{
	public function index()
	{
		$actions = array();
		$actions["create_user"] = site_url("usersdata/form");
		
		$data = array();
		$data["title"] = lang("Users");
		$this->view('list', $data, $actions);
	}
	
	public function setListParameters()
	{
        $this->load->helper('action');
        
        $model = new Model("UsersData", "ud", array("id"=>"id", "identification"=>"identification"));
        $model->setNumerics(array("ud.id"));
        
        $user = new Model("Users", "u", array( "id" => "idU", "name"=>"name", "last_name"=>"last_name", "email"=>"email"));
        $user->setNumerics(array("u.id"));
        $user->setRelation("user");
        
        $profile = new Model("Profiles", "p");
        $profile->setRelation("profile");
        $profile->setModelJoin("u");
        
        $city = new Model("Cities", "c", array("name"=>"nameC"));
        $city->setRelation("city");
        $city->setModelJoin("u");
        
        $country = new Model("Countries", "co", array("name"=>"nameCo"));
        $country->setRelation("country");
        $country->setModelJoin("c");
        
        $relations = array();
        array_push($relations, $user);
        array_push($relations, $profile);
        array_push($relations, $city);
        array_push($relations, $country);
        
        $actions = array();
        array_push($actions, new Action("usersdata", "form", "edit"));        
        array_push($actions, new Action("usersdata", "delete", "delete", false));        
        
        $this->model     = $model;
        $this->actions   = $actions;
        $this->relations = $relations;
	}
	
    public function form ($identifier = 0)
    {
        $this->loadRepository("Profiles");
        $this->loadRepository("Countries");
        $this->loadRepository("Cities");
        
        $id         = ($this->input->post("id") > 0) ? $this->input->post("id") : 0;
        $idProfile  = $this->input->post("idProfile");
        $userId     = 0;
        $language   = $this->input->post("language");
        $name       = $this->input->post("name");
        $lastName   = $this->input->post("lastName");
        $email      = $this->input->post("email");
        $idCity     = $this->input->post("idCity");
        $idCountry  = $this->input->post("idCountry");
        $identification = $this->input->post("identification");
        
        if ($identifier > 0){
            $output = $this->rest->get('usersdata/userdata/', array("id"=>$identifier));

            if ($output->status){
                $user       = $output->data;
                $userId     = $user->user->id;
                $language   = $user->user->language;
                $name       = $user->user->name;
                $lastName   = $user->user->last_name;
                $email      = $user->user->email;
                $idCity     = $user->user->city->id;
                $idCountry  = $user->user->city->country->id;
                $language   = $user->user->language;
                $id         = $user->id;
                $identification = $user->identification;
                $idProfile  = $user->profile->id;
                
                foreach($user->interests as $aInterest){
                    $interestsUser[] = $aInterest->id;
                }
            }
        }

        $actions = array();
        $actions["return_user"] = site_url("usersdata/index");
        
        $data = array();
        $data["title"]      = lang("Users");
        $data["language"]   = $language;
        $data["name"]       = $name;
        $data["last_name"]  = $lastName;
        $data["email"]      = $email;
        $data["id"]         = $id;
        $data["idCountry"]  = $idCountry;
        $data["idProfile"]  = $idProfile;
        $data["countries"]  = $this->Countries->findAll();
        $data["cities"]     = $this->Cities->findBy(array("country"=>$idCity));
        $data["idCity"]     = $idCity;
        $data["identification"] = $identification;
        $data["languages"]      = $this->config->config["languages"];
        $data["profiles"]   = $this->Profiles->findAll();
        
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
        $this->form_validation->set_rules('name', 'lang:name', 'required');
        $this->form_validation->set_rules('lastName', 'lang:last_name', 'required');
        $this->form_validation->set_rules('email', 'lang:email', 'required|callback_existUser['.$this->input->post("id").']');
        $this->form_validation->set_message('existUser', lang('user_exist'));
        $this->form_validation->set_rules('identification', 'lang:identification', 'required');
        
        if ($this->form_validation->run($this)){
            if ($this->input->post("id") > 0){
                $output = $this->rest->post('usersdata/userdata', $this->input->post()); 
            }else{
                $output = $this->rest->put('usersdata/userdata', $this->input->post()); 
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
        $output = $this->rest->get('usersdata/exist_user', array("id"=>$id, "email"=>$email)); 
        return $output->exist;
    }
    
    public function delete ()
    {
        $data    = array();
        $message = "";
        $warning = "";
        $error   = "";
        
        $output = $this->rest->delete('usersdata/userdata', array("id"=>$this->input->post("id")));
        
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
}