<?php
class Menu
{
    private $sections;

    public function __construct ()
    {
        $this->sections  = array();
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function anyadeItem ($section, $title, $url)
    {
        $items = array();

        if (isset ($this->sections[$section]) && is_array($this->sections[$section]))
        {
            $items = $this->sections[$section];
        }

        $items[$title] = $url;
        $this->sections[$section] = $items;
    }

    public function toXHTML ($id)
    {
        $return = "";
        
        $xhtmlSections = Soporte::abreTag("ul", "id='".$id."'");
        
        foreach ($this->sections as $titleSeccion => $items)
        {
            if (count($items) == 1){
                foreach ($items as $titleItem => $urlSection){
                    $xhtmlSections.= Soporte::abreTag("li");
                    $xhtmlSections.= Soporte::abreTag("a", "href='".$urlSection."'");
                    $xhtmlSections.= Soporte::creaTag("i","", "class='foundicon-checkmark'");
                    $xhtmlSections.= $titleItem;
                    $xhtmlSections.= Soporte::cierraTag("a");
                    $xhtmlSections.= Soporte::creaTag("div","", "class='arrow-selected'");
                    $xhtmlSections.= Soporte::cierraTag("li");
                    break;
                }
                continue;
            }
            
            $xhtmlSections.= Soporte::abreTag("li");
            $xhtmlSections.= Soporte::abreTag("a", "href='#'");
            $xhtmlSections.= Soporte::creaTag("i","", "class='foundicon-checkmark'");
            $xhtmlSections.= $titleSeccion;
            $xhtmlSections.= Soporte::creaTag("i","", "class='arrow-menu icon-angle-left'");
            $xhtmlSections.= Soporte::creaTag("div","", "class='arrow-selected'");
            $xhtmlSections.= Soporte::cierraTag("a");
            
            $xhtmlSections.= Soporte::abreTag("ul", "class='submenu'");
            foreach ($items as $titleItem => $urlSection)
            {
            	$xhtmlSections.= Soporte::abreTag("li");
            	$xhtmlSections.= Soporte::creaTag("a", $titleItem, "href='".$urlSection."'");
            	$xhtmlSections.= Soporte::cierraTag("li");
            }
            $xhtmlSections.= Soporte::cierraTag("ul");
            
            $xhtmlSections.= Soporte::cierraTag("li");
        }

        $xhtmlSections.= Soporte::abreTag("li");
        $xhtmlSections.= Soporte::abreTag("a", "href='".base_url()."login/logout'");
        $xhtmlSections.= Soporte::creaTag("i","", "class='foundicon-checkmark'");
        $xhtmlSections.= Soporte::creaTag("span", lang("logout"));
        $xhtmlSections.= Soporte::cierraTag("a");
        $xhtmlSections.= Soporte::abreTag("ul");
        $xhtmlSections.= Soporte::cierraTag("ul");
        $xhtmlSections.= Soporte::cierraTag("li");

        $xhtmlSections.= Soporte::cierraTag("ul");
        
        $return = Soporte::creaTag("div", $xhtmlSections,"class='span2 sidebar-nav side_nav' id='side_nav'");

        return $return;
    }
}
?>