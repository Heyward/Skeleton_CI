<?php
$html = form_open('/profiles/persistProfilePermission/', array("name"=>"form", "id"=>"form", "class"=>"form-horizontal"), array('id' => $id));
$html .= Soporte::abreTag("fieldset");

$html .= Soporte::abreTag("table", "border='0' cellpadding='0' cellspacing='0' width='100%'");
foreach ($sections as $aSection){
    if (count($aSection->getPermissions()) > 0){
        $html .= Soporte::abreTag("tr");
        $html .= Soporte::abreTag("td", "colspan='3'");
        $html .= Soporte::creaTag("h2", lang($aSection->getLabel()));
        $html .= Soporte::cierraTag("td");
        $html .= Soporte::cierraTag("tr");
        
        $index = 0;
        $html .= Soporte::abreTag("tr");
        foreach ($aSection->getPermissions() as $aPermission){
            $selected = false;
            foreach ($permissionsProfile as $aPermissionProfile) {
                $selected = ($aPermission->getId() == $aPermissionProfile->getId());
                if ($selected){
                    break;
                }
            }
            
            $html .= Soporte::abreTag("td");
            $html .= form_checkbox(array("name" => "permission[]","value" => $aPermission->getId(),"checked" => $selected,"class" => "uniform_on"));
            $html .= lang($aPermission->getLabel());
            $html .= Soporte::cierraTag("td");
            
            $index++;
            if ($index% 3 == 0){
                $html .= Soporte::cierraTag("tr");
                $html .= Soporte::abreTag("tr");
            }
        }
        
        $html .= Soporte::cierraTag("tr");
        $html .= Soporte::abreTag("tr");
        $html .= Soporte::creaTag("td", "<br/><br/>", "colspan='3'");
        $html .= Soporte::cierraTag("td");
        $html .= Soporte::cierraTag("tr");
   }
}
$html .= Soporte::cierraTag("table");

$html .= Soporte::abreTag("div", "class='form-actions'");
$html .= form_button(array('type'=>'submit', 'class'=>'btn btn-primary', 'content'=>lang('send')));
$html .= Soporte::cierraTag("div");
    

$html .= Soporte::abreTag("div","id='alert' class='alert alert' style='display: none;'");
$html .= Soporte::creaEnlaceTexto("x", "#", "class='close hide_alert'");
$html .= Soporte::creaTag("div", validation_errors(), "id='message'");
$html .= Soporte::cierraTag("div");
    
$html .= Soporte::cierraTag("fieldset");
$html .= form_close();

echo $html;
?>
