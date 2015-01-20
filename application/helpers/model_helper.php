<?php
class Model
{
    private $model;
    private $rename;
    private $fields;
    private $relation;
    private $conditions;
    private $numerics;
    private $typesDate;
    private $typesTime;
    private $modelJoin;
    private $groupBy;

    public function __construct ($model, $rename, array $fields = array())
    {
        $this->model        = $model;
        $this->rename       = $rename;
        $this->fields       = $fields;
        $this->numerics     = array();
        $this->conditions   = array();
        $this->typesDate    = array();
        $this->typesTime    = array();
        $this->modelJoin    = false;
        $this->groupBy      = array();
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModelJoin($modelJoin)
    {
        $this->modelJoin = $modelJoin;
    }

    public function getModelJoin()
    {
        return $this->modelJoin;
    }

    public function setRename($rename)
    {
        $this->rename = $rename;
    }

    public function getRename()
    {
        return $this->rename;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

    public function getRelation()
    {
        return $this->relation;
    }

    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function setNumerics(array $numerics)
    {
        $this->numerics = $numerics;
    }

    public function getNumerics()
    {
        return $this->numerics;
    }

    public function setTypesDate(array $typesDate)
    {
        $this->typesDate = $typesDate;
    }

    public function getTypesDate()
    {
        return $this->typesDate;
    }

    public function setTypesTime(array $typesTime)
    {
        $this->typesTime = $typesTime;
    }

    public function getTypesTime()
    {
        return $this->typesTime;
    }
    
    public function setGroupBy(array $group) 
    {
        $this->groupBy = $group;
    }

    public function getGroupBy() 
    {
        return $this->groupBy;
    }
}