<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Model
 *
 * @author Portatil 1004
 */
class MY_Model extends CI_Model {

    private $table = '';
    private $keys = array();
    private $fields = array();
    private $compuesta = '';

    function __construct() {
	parent::__construct();
    }

    /**
     * inicializa el modelo de la tabla
     *
     * @param string $nombre_tabla  nombre de la tabla del modelo
     */
    function init($nombre_tabla) {
	$this->table = $nombre_tabla;
	$this->fields = $this->db->field_data($this->table);
	foreach ($this->fields as $field) {
	    if ($field->primary_key) {
		$this->keys[] = $field->name;
	    }
	}
	$this->compuesta = count($this->keys) > 1;
    }

    /**
     * Insertar datos manualmente a una tabla
     *
     * @param mixed $data array asociativo u objeto con los datos a insertar
     * @return int llave insertada si el id es autoincrementable
     */
    public function insert($data) {
	$this->db->insert($this->table, $data);
	return $this->db->insert_id();
    }

    /**
     * Actualizar datos manualmente a una tabla
     *
     * @param mixed $set array asociativo u objeto con los datos a insertar
     * @param mixed $where array asociativo filtro del update
     */
    public function edit($set, $where) {
	$this->db->update($this->table, $set, $where);
	return $this->db->affected_rows();
    }

    /**
     * Insert automatico directamente desde el form
     *
     * @return int llave insertada si el id es autoincrementable
     */
    public function insert_post() {
	$data = array();
	foreach ($this->fields as $field) {
	    $retorno = NULL;
	    if ($this->input->post($field->name) === FALSE)
		continue;

	    if ($field->type == 'date') {
		$retorno = '0000-00-00';
	    }
	    if ($field->type == 'datetime' || $field->type == 'timestamp') {
		$retorno = '0000-00-00 00:00:00';
	    }
	    if ($field->type == 'time') {
		$retorno = '00:00:00';
	    }
	    if ($this->compuesta == TRUE || ($this->compuesta == FALSE && $field->primary_key == FALSE)) {
		if ($field->name == 'password') {
		    $data[$field->name] = ($this->input->post($field->name) ? md5($this->input->post($field->name)) : $retorno );
		} else {
		    $data[$field->name] = ($this->input->post($field->name) ? $this->input->post($field->name) : $retorno );
		}
	    }
	}
	$this->db->insert($this->table, $data);
	return $this->db->insert_id();
    }

    /**
     * Update automatico directamente desde el post
     * @return int numero de datos afectados
     */
    public function edit_post() {
	//Where consulta de un id interno
	$where = array();
	foreach ($this->keys as $key) {
	    $where[$key] = $this->input->post($key);
	}
	$actuales = $this->_get_id($where)->first_row('array');

	$actualiza = array();
	foreach ($actuales as $key => $value) {
	    if (!in_array($key, $this->keys)) {
		if ($this->input->post($key) !== FALSE && $value != $this->input->post($key)) {
		    $actualiza[$key] = $this->input->post($key);
		}
	    }
	}

	if (count($actualiza) > 0) {
	    $this->db->update($this->table, $actualiza, $where);
	    return $this->db->affected_rows();
	}
	return 0;
    }

    /**
     * Eliminar un solo registro
     *
     * @param mixed recibe los parametros de llave primaria en en orden de la tabla  param1, param2 ...
     * @return int numero de rows afectados
     */
    public function delete() {
	$args = func_get_args();
	$where = $this->_read_args($args);
	if (count($where) > 0) {
	    $this->db->delete($this->table, $where);
	    return $this->db->affected_rows();
	}
	return 0;
    }

    /**
     * Consulta todos los campos
     *
     * @return object resultset todos los campos
     */
    public function get_all($order_by='', $where = FALSE) {
        if(is_array($where)){
           $this->db->where($where);
        }
	if ($order_by != '') {
	    $query = $this->db->order_by($order_by);
	}
	$query = $this->db->get($this->table);
	return $query;
    }

    /**
     * Consulta solo un registro
     * @param mixed recibe los parametros de llave primaria en en orden de la tabla  param1, param2 ...
     * @return object resultset con un solo campo
     */
    public function get_id() {
	$args = func_get_args();
	$where = $this->_read_args($args);
	$query = $this->db->get_where($this->table, $where);
	return $query;
    }

    /**
     * Funcion interna que permite organizar los parametros de keys
     *
     * @param  array $args array no asociativo que trae los keys de la tabla en el orden de la misma
     * @return array $where array asociativo de keys vs valor
     */
    private function _read_args($args) {
	$i = 0;
	$where = array();
	foreach ($this->keys as $key) {
	    if (count($args) > $i) {
		if ($args[$i] != '%') {
		    $where[$key] = $args[$i];
		}
	    }
	    $i++;
	}
	return $where;
    }

    /**
     * Consulta interna manual de get_id
     * @param array $where filtro del select
     * @return object $query resultset de campos
     */
    private function _get_id($where) {
	$query = $this->db->get_where($this->table, $where);
	return $query;
    }
    
    /**
     * Me devuelve el nombre de mi tabla
     * @return string nombre tabla
     */
    public function get_table() {
        return $this->table;
    }    

    /**
     *  Integración con jquery datatables estrae los datos y lee los filtos
     * 
     * @param array $data filtros enviados por post() desde el datatable
     * @param array $columns arreglo de columnas a mostrar con alias si es necesario array('id','nombre','category.name')
     * @param mixed $relations_models  nombre del modelo con el que se relaciona ó array de modelos con los que se relaciona
     * @return array representación sin json del data de que necesita el table.
     */
    function datatable($data, $columns, $relations_models=FALSE, $where=FALSE , $search=array()) {
        $columns_ori = $columns;
        $me = $this->get_table();

        /* Prepare array of columns for results */
        if ($relations_models) {
            $columns_two = array();
            foreach ($columns as $k => $value) {
                if (strpos($value, '.') === FALSE) {
                    $value = $me . "." . $value;
                }
                $columns_two[] = $value;

                $c = preg_split("/AS/", $value);
                if (count($c) > 1) {
                    $columns_ori[$k] = trim($c[1]);
                }
            }
            $columns = $columns_two;
        }

        /* Select and relations */
        $this->db->select($columns);
        $this->db->from($me);
        
        /*$relation_models = array(
                        array('paciente', 'idPaciente', 'idPaciente'),
                        array('servicios', 'idEspecialidad', 'idServicios'),
                        array('tratamientos', 'idTratamiento', 'idTratamientos'),
                        array('medico', 'idMedico', 'idMedico')
                );*/
        if ($relations_models) {
            for($i=0; $i<count($relations_models); $i++) {//tabla de join, llave primaria, llave foranea
                $table = $relations_models[$i][0];
                $this->db->join($table, $me . "." . $relations_models[$i][1] . "  = $table.".$relations_models[$i][2]);
            }
        }
        /* Paging */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $this->db->limit($data['iDisplayLength'], $data['iDisplayStart']);
        }
        /* Sorting */
        for ($i = 0; $i < intval($data['iSortingCols']); $i++) {
            if ($data['bSortable_' . intval($data['iSortCol_' . $i])] == "true") {
                $this->db->order_by($data['mDataProp_'.intval($data['iSortCol_' . $i])], $data['sSortDir_' . $i]);
            }
        }
        /* Filtering */
        if ($data['sSearch'] != "") {
            for ($i = 0; $i < intval($data['iColumns']); $i++) {
                if($data['mDataProp_'.$i]!='options_dt')
                    $this->db->or_like($search[$i], $data['sSearch']);
            }
        }

        /* Individual column filtering */
        for ($i = 0; $i < intval($data['iColumns']); $i++) {
            if ($data['bSearchable_' . $i] == "true" && $data['sSearch_' . $i] != '') {
                if($data['mDataProp_'.$i]!='options_dt')
                    $this->db->like($data['mDataProp_'.$i], $data['sSearch_' . $i]);
            }
        }

        if(is_array($where)){
           $this->db->where($where);
        }

        $query = $this->db->get();
        //echo $this->db->last_query();
        /*
         * Output
            "iTotalDisplayRecords" => $query->num_rows(),
         */
        $output = array(
            "sEcho" => intval($data['sEcho']),
            "iTotalRecords" => $this->get_all($where)->num_rows(),
            "iTotalDisplayRecords" => $this->get_all($where)->num_rows(),
            "aaData" => array()
        );
        foreach ($query->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < intval($data['iColumns']); $i++) {
                if($data['mDataProp_'.$i]!='options_dt')
                    eval('$row["'.$data['mDataProp_'.$i].'"] = $aRow->'.$data['mDataProp_'.$i].';');
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }
}