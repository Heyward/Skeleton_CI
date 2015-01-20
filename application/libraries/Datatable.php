<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

/**
 * libreria creacion data para datatable
 *
 * @author Administrador
 */
class Datatable
{
    private $em;

    private $modelo;
    private $relaciones;
    private $data;

    private $camposSelect;
    private $nombresCampos;
    private $columnas;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('doctrine');
        $this->CI->lang->load('general');
        $this->em = $this->CI->doctrine->em;
    }
    
    /*OBTIENE ARRAY DE DATOS PARA DATATABLE*/
    function getData($data, Model $modelo, $relaciones)
    {
        $this->modelo       = $modelo;
        $this->relaciones   = $relaciones;
        $this->data         = $data;

        $this->columnas         = array();
        $this->camposSelect     = array();
        $this->nombresCampos    = array();

        /*RECUPERA LOS CAMPOS PARA LAS CONSULTAS*/
        foreach ($modelo->getFields() as $clave => $valor) {
            $campo = $modelo->getRename() . "." . $clave;

            array_push($this->camposSelect,     $campo . " " . $valor);
            array_push($this->columnas,         $campo);
            array_push($this->nombresCampos,    $valor);
        }

        foreach ($relaciones as $unaRelacion) {
            foreach ($unaRelacion->getFields() as $clave => $valor) {
                $campo = $unaRelacion->getRename() . "." . $clave;

                array_push($this->camposSelect,     $campo . " " . $valor);
                array_push($this->columnas,         $campo);
                array_push($this->nombresCampos,    $valor);
            }
        }

        /*ARMA CADA PARTE DE LA CONSULTA DQL*/
        $select = $this->getSelect();
        $sWhere = $this->getWhere();
        $sGroup = $this->getGroupBy();
        $sOrder = $this->getOrder();
        $sql    = $select . $sWhere . $sGroup . $sOrder;

        /*EJECUTA LAS CONSULTAS SIN LAS CONDICIONES WHERE*/
        $queryTotal = $this->em->createQuery($select . $sGroup)->getResult();
        $query      = $this->em->createQuery($sql);

        /*EJECUTA CONSULTA CON TODAS LAS CONDICIONES SIN PAGINACION*/
        $numeroResultados = count($query->getResult());
        /*SETEA LIMIT PARA PAGINACION Y NUEMERO DE RESULTADOS*/
        if (isset($data['iDisplayStart']) && $data['iDisplayLength'] != '-1') {
            $query->setMaxResults($data['iDisplayLength']);
            $query->setFirstResult($data['iDisplayStart']);
        }

        /*EJECUTA CONSULTA CON TODAS LAS CONDICIONES*/
        $resultados = $query->getResult();
        /*RETORNA ARRAY CON LOS DATOS DE LA CONSULTA*/
        $retorno    = $this->getArrayData($resultados, $numeroResultados, count($queryTotal));

        return $retorno;
    }

    /*ARMA EL SELECT DE LA CONSULTA*/
    private function getSelect()
    {
        $retorno    = "SELECT ";
        $separador  = "";

        /*ADICIONA CADA CAMPO A MOSTRAR EN EL SELECT*/
        foreach ($this->camposSelect as $unCampo) {
            $retorno   .= $separador . $unCampo;
            $separador  = ",";
        }

        /*ADICIONA EL FROM DE LA CONSULTA*/
        $retorno .= " FROM models\\" . $this->modelo->getModel() . " " . $this->modelo->getRename() . " ";

        /*ADICIONA LOS JOINS DE LA CONSULTA*/
        foreach ($this->relaciones as $unaRelacion) {
            $renombreModelo     = $this->modelo->getRename();
            
            if ($unaRelacion->getModelJoin()){
                $renombreModelo = $unaRelacion->getModelJoin(); 
            }
            
            $renombreRelacion   = $unaRelacion->getRename();
            $relacion           = $unaRelacion->getRelation();

            $retorno .= " JOIN " . $renombreModelo . "." . $relacion . " " . $renombreRelacion . " ";
        }

        return $retorno;
    }

    /*ARMA EL WHERE DE LA CONSULTA*/
    private function getWhere()
    {
        $retorno    = "";

        /*RECUPERA LOS CAMPOS NUMERICOS PARA DISCRIMINAR 'LIKE'*/
        $numericos  = $this->modelo->getNumerics();

        foreach ($this->relaciones as $unaRelacion){
            $numericos = array_merge($numericos, $unaRelacion->getNumerics());
        }

        /*RECUPERA LOS CAMPOS FECHAS PARA DISCRIMINAR 'LIKE'*/
        $fechas = $this->modelo->getTypesDate();

        foreach ($this->relaciones as $unaRelacion){
            $fechas = array_merge($fechas, $unaRelacion->getTypesDate());
        }

        /*RECUPERA LOS CAMPOS HORA PARA DISCRIMINAR 'LIKE'*/
        $horas = $this->modelo->getTypesTime();

        foreach ($this->relaciones as $unaRelacion){
            $horas = array_merge($horas, $unaRelacion->getTypesTime());
        }

        /*SI HAY UN DATO A BUSCAR*/
        if (isset($this->data['sSearch']) && $this->data['sSearch'] != "") {
            $retorno = "WHERE (";

            for ($i = 0; $i < intval($this->data['iColumns']); $i++) {
                $hayColumna = (empty($this->columnas[$i]) == false);

                /*SI LA COLUMNA NO ES VACIA, COMO POR EJEMPLO COLUMNA DE BOTONES*/
                if ($hayColumna) {
                    $searchNumerico     = (is_numeric($this->data['sSearch']));
                    $searchFecha        = ($this->fechaValida(($this->data['sSearch'])));
                    $searchHora         = ($this->horaValida(($this->data['sSearch'])));
                    $columnaNumerica    = in_array($this->columnas[$i], $numericos);
                    $columnaFecha       = in_array($this->columnas[$i], $fechas);
                    $columnaHora        = in_array($this->columnas[$i], $horas);
                    $columnaTexto       = ($columnaFecha == false && $columnaHora == false && $columnaNumerica == false);

                    /*SI EL DATO A BUSCAR ES NUMERICO A LOS CAMPOS NUMERICOS COMPARA CON IGUAL (=), LOS DEMAS CON 'LIKE'*/
                    if ($searchNumerico) {
                        if ($columnaNumerica) {
                            $retorno .= $this->columnas[$i] . " = " . $this->data['sSearch'] . " OR ";
                        }

                        if ($columnaTexto) {
                            $retorno .= $this->columnas[$i] . " LIKE '%" . $this->data['sSearch'] . "%' OR ";
                        }
                    }

                    /*SI EL DATO A BUSCAR NO ES NUMERICO COMPARA CON 'LIKE' LOS CAMPOS NO NUMERICOS*/
                    if ($searchNumerico == false) {
                        if ($columnaTexto) {
                            $retorno .= $this->columnas[$i] . " LIKE '%" . $this->data['sSearch'] . "%' OR ";
                        }
                    }

                    /*SI EL DATO A BUSCAR ES FECHA A LOS CAMPOS FECHA COMPARA CON IGUAL (=), LOS DEMAS CON 'LIKE'*/
                    if ($searchFecha) {
                        if ($columnaFecha) {
                            $retorno .= $this->columnas[$i] . " = '" . $this->data['sSearch'] . "' OR ";
                        }
                    }

                    /*SI EL DATO A BUSCAR ES HORA A LOS CAMPOS FECHA COMPARA CON IGUAL (=), LOS DEMAS CON 'LIKE'*/
                    if ($searchHora) {
                        if ($columnaHora) {
                            $retorno .= $this->columnas[$i] . " = '" . $this->data['sSearch'] . "' OR ";
                        }
                    }
                }
            }

            $retorno = substr_replace($retorno, "", -3);
            $retorno .= ')';
        }

        /*SI EXISTE ALGO EN EL FILTRO EXTERNO DE FECHAS*/         
        if (isset($this->data['fecha_inicio']))
        {
            $sentencia = "";
            if ($retorno == "") {
                $sentencia = "WHERE (";
            } else {
                $sentencia .= " AND (";
            }
            
            for ($i = 0; $i < intval($this->data['iColumns']); $i++) {
                $hayColumna     = (empty($this->columnas[$i]) == false);
        
                if ($hayColumna){
                    $columnaFecha   = in_array($this->columnas[$i], $fechas);
                    $fechaInicio    = $this->data['fecha_inicio'];
                    $fechaFin       = $this->data['fecha_fin'];
                    
                    if ($columnaFecha && Soporte::contieneValor($fechaInicio)){
                        if (Soporte::contieneValor($fechaFin)){
                            $retorno .= $sentencia;
                            $retorno .= $this->columnas[$i] . " >= '" . $fechaInicio . "' AND ".$this->columnas[$i] . " <= '" . $fechaFin. "' OR ";
                        }
            
                        if (Soporte::contieneValor($fechaFin) == false){
                            $retorno .= $sentencia;
                            $retorno .= $this->columnas[$i] . " >= '" . $fechaInicio . "' OR ";
                        }
                    
                    $retorno = substr_replace($retorno, "", -3);
                    $retorno .= ')';
                    }
                }
            }
            
        }
        
        /*BUSQUEDAS INDIVIDUALES POR COLUMNA*/
        for ($i = 0; $i < count($this->columnas); $i++) {
            if (isset($this->data['bSearchable_' . $i]) &&  $this->data['bSearchable_' . $i] == "true" && $this->data['sSearch_' . $i] != '') {
                $sentencia = "";

                if ($retorno == "") {
                    $sentencia = "WHERE ";
                } else {
                    $sentencia .= " AND ";
                }

                $searchNumerico     = (is_numeric($this->data['sSearch_' . $i]));
                $searchFecha        = ($this->fechaValida(($this->data['sSearch_' . $i])));
                $searchHora         = ($this->horaValida(($this->data['sSearch_' . $i])));
                $columnaNumerica    = in_array($this->columnas[$i], $numericos);
                $columnaFecha       = in_array($this->columnas[$i], $fechas);
                $columnaHora        = in_array($this->columnas[$i], $horas);
                $columnaTexto       = ($columnaFecha == false && $columnaHora == false && $columnaNumerica == false);

                if ($columnaNumerica && $searchNumerico){
                    $retorno .= $sentencia;
                    $retorno .= $this->columnas[$i] . " = " . $this->data['sSearch_' . $i] . " ";
                }

                if ($columnaFecha && $searchFecha){
                    $retorno .= $sentencia;
                    $retorno .= $this->columnas[$i] . " = '" . $this->data['sSearch_' . $i] . "' ";
                }

                if ($columnaHora && $searchHora){
                    $retorno .= $sentencia;
                    $retorno .= $this->columnas[$i] . " = '" . $this->data['sSearch_' . $i] . "' ";
                }

                if ($columnaTexto){
                    $retorno .= $sentencia;
                    $retorno .= $this->columnas[$i] . " LIKE '%" . $this->data['sSearch_' . $i] . "%' ";
                }
            }
        }

        /*RECUPERA LAS CONDICIONES SETEADAS EN EL MODELO*/
        $condiciones = $this->modelo->getConditions();

        foreach ($this->relaciones as $unaRelacion) {
            $condiciones = array_merge($condiciones, $unaRelacion->getConditions());
        }

        /*SI HAY POR LO MENOS UNA CONDICION LA ADICIONA*/
        if (count($condiciones) > 0) {
            if ($retorno == "") {
                $retorno = "WHERE ";
            } else {
                $retorno .= " AND ";
            }


            foreach ($condiciones as $unaCondicion) {
                $retorno .= $unaCondicion . " AND ";
            }

            $retorno = substr_replace($retorno, "", -4);
        }

        return $retorno;
    }

    /*ARMA EL ORDER BY DE LA CONSULTA*/
    private function getOrder()
    {
        $retorno = "";

        /*SI HAY CAMPO ORDENADOR ADICIONA ORDER BY*/
        if (isset($this->data['iSortCol_0'])) {
            $retorno = " ORDER BY ";

            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($this->data['bSortable_' . intval($this->data['iSortCol_' . $i])] == "true") {
                    $retorno .= $this->columnas[intval($this->data['iSortCol_' . $i])] . " " . $this->data['sSortDir_' . $i] . ", ";
                }
            }

            if ($retorno == " ORDER BY ") {
                $retorno = "";
            }
            
            $retorno = substr_replace($retorno, "", -2);
        }

        return $retorno;
    }
    
    /*ARMA EL GROUP BY DE LA CONSULTA*/
    public function getGroupBy() 
    {
        $retorno = "";
        
        $group = $this->modelo->getGroupBy();
        
        if (!empty($group) && is_array($group)){
            $retorno = " GROUP BY " . implode(", ", $group);
        }
        
        return $retorno;
    }

    /*CONSTRUYE EL ARRAY A RETORNAR CON LOS DATOS DE LA CONSULTA*/
    private function getArrayData($resultados, $numeroResultados, $numeroTotal)
    {
        $registros = array();

        foreach ($resultados as $unRegistro) {
            $row = array();

            foreach ($this->nombresCampos as $unNombre) {
                array_push($row, $unRegistro[$unNombre]);
            }

            array_push($registros, $row);
        }

        $retorno = array();
        $retorno["sEcho"] = intval($this->data['sEcho']);
        $retorno["iTotalRecords"] = $numeroTotal;
        $retorno["iTotalDisplayRecords"] = $numeroResultados;
        $retorno["aaData"] = $registros;

        return $retorno;
    }

    private function fechaValida($fecha)
    {
        $reg = "(([0-9]{4})[\-]([0-9]{1,2})[\-]([0-9]{1,2}))";
        return preg_match($reg, $fecha);
    }

    private function horaValida($hora)
    {
        $reg = "(([0-9]{1,2})[\:]([0-9]{1,2})[\:]([0-9]{1,2}))";
        return preg_match($reg, $hora);
    }
}
