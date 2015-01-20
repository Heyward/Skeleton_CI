<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Builder extends MY_Controller 
{
    private $pathModels;
    
    public function __construct(){
        parent::__construct();
        $DS         = DIRECTORY_SEPARATOR;
        $this->pathModels = dirname(__FILE__) . $DS . ".." .$DS . ".." . $DS . ".." . $DS . "models" . $DS;
    }
    
    public function setListParameters(){}
    
    public function index ()
    {
        $data = array();
        $data["title"]  = "Builder";
        $data["models"] = $this->getModels();
        $this->view('form', $data);
    }
    
    public function generateModule(){
        $this->form_validation->set_rules('model', 'model', 'required');
        $this->form_validation->set_rules('singular', 'singular', 'required');
        
        if ($this->form_validation->run($this)){
            $file = $this->input->post("model");
            $singular = $this->input->post("singular");
            
            $classes = $this->file_get_php_classes($this->pathModels . $file);
            
            foreach ($classes as $class => $methods) {
                $pattern = "/^set/";
                $methodsName = array();
                
                foreach ($methods as $aMethod) {
                    if (preg_match($pattern, $aMethod)){
                        $aMethod = str_replace("set", "", $aMethod);
                        $methodsName[] = $aMethod;
                    }
                }
                
                $model          = $class;
                $modelMin       = strtolower($model);
                $modelMay       = strtoupper($model);
                $singular       = strtolower($singular);
                $singularMay    = ucfirst($singular);
                $rename         = substr($singular, 0, 1);
                $setMethodsPost = $this->getSetMethodsPost($singular, $methodsName);
                $setMethodsPut  = $this->getSetMethodsPut($singular, $methodsName);
                $fieldsModel    = $this->getFieldsModel($methodsName);
                $fieldsPost     = $this->getFieldsPost($methodsName);
                $fieldsStdClass = $this->getFieldsStdClass($singular, $methodsName);
                $fieldsDataView = $this->getFieldsDataView($methodsName);
                $formValidation = $this->getFormValidation($methodsName);
                $fieldsLang     = $this->fieldsLang($methodsName);
                $colTable       = $this->getColTable($methodsName);
                $fieldsForm     = $this->getFieldsForm($methodsName);
                $nullDataTable  = $this->getNullDataTable($methodsName);
                $rules          = $this->getRules($methodsName);
                $messagesRules  = $this->getMessagesRules($methodsName);
                
                $DS = DIRECTORY_SEPARATOR;
                $dirFiles       = dirname(__FILE__). $DS . "..". $DS . "files" . $DS;
                $dirSkeleton    = $dirFiles . "skeleton_module" . $DS;
                $dirTmp         = $dirFiles . "tmp" . $DS;
                $dirCreate      = $dirTmp . $modelMin . $DS;
                
                if (is_dir($dirCreate)){
                    recursive_remove_directory($dirCreate);
                }
                
                if (mkdir($dirCreate, 0777)){
                    if (mkdir($dirCreate . "api", 0777)){
                        $content = file_get_contents($dirSkeleton . "api" . $DS . "api.sk");
                        $content = str_replace("__MODEL__", $model, $content);
                        $content = str_replace("__SINGULAR__", $singular, $content);
                        $content = str_replace("__SET_METHODS_POST__", $setMethodsPost, $content);
                        $content = str_replace("__SET_METHODS_PUT__", $setMethodsPut, $content);
                        
                        $path = $dirCreate . "api" . $DS . $modelMin . ".php";
                        $file = fopen($path, "w");
                        fwrite($file, $content);
                        fclose($file);
                    }
                    
                    if (mkdir($dirCreate . "controllers", 0777)){
                        $content = file_get_contents($dirSkeleton . "controllers" . $DS . "controller.sk");
                        $content = str_replace("__MODEL__", $model, $content);
                        $content = str_replace("__SINGULAR__", $singular, $content);
                        $content = str_replace("__MODEL_MIN__", $modelMin, $content);
                        $content = str_replace("__RENAME__", $rename, $content);
                        $content = str_replace("__FIELDS_MODEL__", $fieldsModel, $content);
                        $content = str_replace("__FIELDS_POST__", $fieldsPost, $content);
                        $content = str_replace("__FIELDS_STD_CLASS__", $fieldsStdClass, $content);
                        $content = str_replace("__FIELDS_DATA_VIEW__", $fieldsDataView, $content);
                        $content = str_replace("__FORM_VALIDATION__", $formValidation, $content);
                        
                        $path = $dirCreate . "controllers" . $DS . $modelMin .".php";
                        $file = fopen($path, "w");
                        fwrite($file, $content);
                        fclose($file);
                    }
    
                    if (mkdir($dirCreate . "language", 0777)){
                        $content = file_get_contents($dirSkeleton . "language" . $DS . "general_lang.sk");
                        $content = str_replace("__MODEL_MAY__", $modelMay, $content);
                        $content = str_replace("__MODEL_MIN__", $modelMin, $content);
                        $content = str_replace("__SINGULAR__", $singular, $content);
                        $content = str_replace("__SINGULAR_MAY__", $singularMay, $content);
                        $content = str_replace("__FIELDS_LANG__", $fieldsLang, $content);
                        
                        $path = $dirCreate . "language" . $DS . "general_lang.php";
                        $file = fopen($path, "w");
                        fwrite($file, $content);
                        fclose($file);
                    }
                    
                    if (mkdir($dirCreate . "views", 0777)){
                        $content = file_get_contents($dirSkeleton . "views" . $DS . "form.sk");
                        $content = str_replace("__MODEL_MIN__", $modelMin, $content);
                        $content = str_replace("__FIELD_FORM__", $fieldsForm, $content);
                        
                        $path = $dirCreate . "views" . $DS . "form.php";
                        $file = fopen($path, "w");
                        fwrite($file, $content);
                        fclose($file);
                        
                        
                        $content = file_get_contents($dirSkeleton . "views" . $DS . "list.sk");
                        $content = str_replace("__COL_TABLE__", $colTable, $content);
                        
                        $path = $dirCreate . "views" . $DS . "list.php";
                        $file = fopen($path, "w");
                        fwrite($file, $content);
                        fclose($file);
                        if (mkdir($dirCreate . "views" . $DS . "js", 0777)){
                            $content = file_get_contents($dirSkeleton . "views" . $DS . "js" . $DS . "form.sk");
                            $content = str_replace("__RULES__", $rules, $content);
                            $content = str_replace("__MESSAGE_RULES__", $messagesRules, $content);
                            
                            $path = $dirCreate . "views" . $DS . "js" . $DS . "form.php";
                            $file = fopen($path, "w");
                            fwrite($file, $content);
                            fclose($file);
                            
                            $content = file_get_contents($dirSkeleton . "views" . $DS . "js" . $DS . "list.sk");
                            $content = str_replace("__MODEL_MIN__", $modelMin, $content);
                            $content = str_replace("__SINGULAR__", $singular, $content);
                            $content = str_replace("__NULLS_DATATABLE__", $nullDataTable, $content);
                            
                            $path = $dirCreate . "views" . $DS . "js" . $DS . "list.php";
                            $file = fopen($path, "w");
                            fwrite($file, $content);
                            fclose($file);
                        }
                    }
                    
                    if ($this->input->post("permissions")){
                        $path = $dirCreate . "controllers" . $DS . $modelMin .".php";
                        $pathLang = $dirCreate . "language" . $DS . "menu_lang.php";
                        $classesController = $this->file_get_php_classes($path);
                        $this->load->helper("setup");
                        createPermissions($classesController);
                        $this->generateLangMenu($classesController, $pathLang);
                    }
                }
            }
            
        }else{
            $error = validation_errors();
            if ($this->input->is_ajax_request() == false){
                $this->index();
            }
        }
    }

    private function getSetMethodsPost($singular, $methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "\$".$singular."->set".$aMethod."(\$this->post('".lcfirst($aMethod)."'));";
            $spaces = "\n\t\t\t";
        }
        return $return;
    }
    
    private function getSetMethodsPut($singular, $methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "\$".$singular."->set".$aMethod."(\$this->put('".lcfirst($aMethod)."'));";
            $spaces = "\n\t\t\t";
        }
        return $return;
    }
    
    private function getFieldsModel($methods){
        $return = "";
        $comma = "";
        foreach ($methods as $aMethod) {
            $return .= $comma. "'".lcfirst($aMethod)."' => '".lcfirst($aMethod)."'";
            $comma = " ,";
        }
        return $return;
    }
    
    private function getFieldsPost($methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "\$".lcfirst($aMethod)." = \$this->input->post('".lcfirst($aMethod)."');";
            $spaces = "\n\t\t";
        }
        return $return;
    }
    
    private function getFieldsStdClass($singular,$methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "\$".lcfirst($aMethod)." = \$".$singular."->".lcfirst($aMethod).";";
            $spaces = "\n\t\t\t\t";
        }
        return $return;
    }
    
    private function getFieldsDataView($methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "\$data['".lcfirst($aMethod)."'] = \$".lcfirst($aMethod).";";
            $spaces = "\n\t\t";
        }
        return $return;
    }
    
    private function getFormValidation($methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "\$this->form_validation->set_rules('".lcfirst($aMethod)."', 'lang:".lcfirst($aMethod)."', 'required');";
            $spaces = "\n\t\t";
        }
        return $return;
    }
    
    private function fieldsLang($methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "\$lang['".lcfirst($aMethod)."'] = '".$aMethod."';";
            $spaces = "\n";
        }
        return $return;
    }
    
    private function getColTable($methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "<th><?=lang('".lcfirst($aMethod)."')?></th>";
            $spaces = "\n\t\t\t";
        }
        return $return;
    }
    
    private function getFieldsForm($methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= "\$fields[lang('".lcfirst($aMethod)."')] = form_input(array('name'=>'".lcfirst($aMethod)."', 'class'=>'span3 focused', 'value'=>\$".lcfirst($aMethod)."));";
            $spaces = "\n\t";
        }
        return $return;
    }
    
    private function getNullDataTable($methods){
        $return = "";
        $comma = "";
        foreach ($methods as $aMethod) {
            $return .= $comma. "null";
            $comma = " ,";
        }
        return $return;
    }
    
    private function getRules($methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= lcfirst($aMethod).": 'required',";
            $spaces = "\n\t\t\t\t";
        }
        return $return;
    }
    
    private function getMessagesRules($methods){
        $return = "";
        $spaces = "";
        foreach ($methods as $aMethod) {
            $return .= $spaces;
            $return .= lcfirst($aMethod).":'<?=lang('required')?>',";
            $spaces = "\n\t\t\t\t";
        }
        return $return;
    }

    private function getModels(){
        $return = array();
        
        foreach (Soporte::leeDirectorio($this->pathModels) as $file => $ext) {
            if ($ext == "php"){
                $return[$file] = str_replace(".php", "", $file);
            }
        }
        
        return $return;
    }
    
    private function file_get_php_classes ($filepath, $onlypublic = true)
    {
        $php_code = file_get_contents($filepath);
        $classes  = $this->get_php_classes($php_code, $onlypublic);
        return $classes;
    }
    
    private function get_php_classes ($php_code, $onlypublic)
    {
        $classes    = array();
        $methods    = array();
        $tokens     = token_get_all($php_code);
        $count      = count($tokens);
        
        for ($i = 2; $i < $count; $i ++) {
            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $class_name = $tokens[$i][1];
                $methods[$class_name] = array();
            }
            
            if ($tokens[$i - 2][0] == T_FUNCTION && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                if ($onlypublic) {
                    if (! in_array($tokens[$i - 4][0], array(T_PROTECTED, T_PRIVATE))) {
                        $method_name = $tokens[$i][1];
                        $methods[$class_name][] = $method_name;
                    }
                } 
                else {
                    $method_name = $tokens[$i][1];
                    $methods[$class_name][] = $method_name;
                }
            }
        }
        
        return $methods;
    }


    private function generateLangMenu($classes, $path){
        $DS = DIRECTORY_SEPARATOR;
        
        $content  = "<?php\n";
        
        foreach ($classes as $aClass => $methods){
            $content .= "\$lang['".$aClass."']='".$aClass."';\n";
            
            foreach ($methods as $aMethod){
                $content .= "\$lang['".$aClass."_".$aMethod."']='".$aClass."_".$aMethod."';\n";
            }
        }
        
        $file = fopen($path, "w");
        fwrite($file, $content);
        fclose($file);
    }
    
    
}