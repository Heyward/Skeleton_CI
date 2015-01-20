<?
function print_form($action, array $fields = array(), array $hiddens = array(), $id = "form", $multipart = false, $columns = 6)
{
    $return = form_open($action, array("name"=>$id, "id"=>$id), $hiddens);
    
    if ($multipart){
       $return = form_open_multipart($action, array("name"=>$id, "id"=>$id), $hiddens);
    }
    
    $cont = 0;
    foreach($fields as $label => $field){
        $return .= Soporte::abreTag("div","class='row collapse'");
        
        $return .= Soporte::abreTag("div","class='large-4 columns'");
        $return .= Soporte::creaTag("span", $label, "class='prefix'");
        $return .= Soporte::cierraTag("div");

        $return .= Soporte::abreTag("div","class='large-8 columns'");
        $return .= $field;
        $return .= Soporte::cierraTag("div");
        
        $return .= Soporte::cierraTag("div");

        $return .= Soporte::abreTag("div","class='row collapse'");
        $return .= Soporte::creaTag("div", "<br/>","class='large-10 columns'");
        $return .= Soporte::cierraTag("div");
        $cont++;
    }
    
    $return .= Soporte::abreTag("div", "class='row collapse'");
    $return .= Soporte::creaTag("div", "&nbsp;", "class='large-2 columns hide-for-small'");
    $return .= form_button(array('type'=>'submit', 'class'=>'large-3 columns button small', 'content'=>lang('send')));
    $return .= Soporte::creaTag("div", "&nbsp;", "class='large-2 columns hide-for-small'");
    $return .= form_button(array('type'=>'reset', 'class'=>'large-3 columns button small', 'content'=>lang('clear')));
    $return .= Soporte::creaTag("div", "&nbsp;", "class='large-4 columns hide-for-small'");
    $return .= Soporte::cierraTag("div");
    
    $suffix  = ($id != "form") ? "_".$id : '';
    $visible = (validation_errors() != "") ? '' : 'style="display: none;"';

    $return .= '<div data-alert id="alert'.$suffix.'" class="alert-box" '.$visible.'>';
    $return .= '<div id="message'.$suffix.'">'.validation_errors().'</div>';
    $return .= '<a href="#" class="close">&times;</a>';
    $return .=  '</div>';
    $return .= form_close();
    
    $return = Soporte::creaTag("div", $return,  "class='large-".$columns." columns large-centered'");
    
    return $return;
}

function print_form_columns($action, array $fields = array(), array $hiddens = array(), $id = "form")
{
    $return = form_open($action, array("name"=>$id, "id"=>$id), $hiddens);
    
    $index = 0;
    $return  .= Soporte::abreTag("div", "class='row'");
    foreach($fields as $label => $field){
        $return .= Soporte::abreTag("div","class='large-2 columns'");
        $return .= Soporte::creaTag("span", $label, "class='prefix'");
        $return .= Soporte::cierraTag("div");

        $return .= Soporte::abreTag("div","class='large-4 columns'");
        $return .= $field;
        $return .= Soporte::cierraTag("div");
        
        $index++;
        if ($index % 2 == 0){
            $return .= Soporte::cierraTag("div");
            $return .= Soporte::abreTag("div", "class='row'");
        }
    }
    $return .= Soporte::cierraTag("div");
    
    $return .= Soporte::abreTag("div", "class='row collapse'");
    $return .= Soporte::creaTag("div", "&nbsp;", "class='large-2 columns hide-for-small'");
    $return .= form_button(array('type'=>'submit', 'class'=>'large-3 columns button small', 'content'=>lang('send')));
    $return .= Soporte::creaTag("div", "&nbsp;", "class='large-2 columns hide-for-small'");
    $return .= form_button(array('type'=>'reset', 'class'=>'large-3 columns button small', 'content'=>lang('clear')));
    $return .= Soporte::creaTag("div", "&nbsp;", "class='large-4 columns hide-for-small'");
    $return .= Soporte::cierraTag("div");
    
    $suffix  = ($id != "form") ? "_".$id : '';
    $visible = (validation_errors() != "") ? '' : 'style="display: none;"';

    $return .= '<div data-alert id="alert'.$suffix.'" class="alert-box" '.$visible.'>';
    $return .= '<div id="message'.$suffix.'">'.validation_errors().'</div>';
    $return .= '<a href="#" class="close">&times;</a>';
    $return .=  '</div>';
    $return .= form_close();
    
    $return = Soporte::creaTag("div", $return,  "class='large-12 columns large-centered'");
    
    return $return;
}