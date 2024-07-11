<?php

class LotType extends Model
{
    private $table_name = 'tbl_lot_types';
    private $pk = 'lot_type_id';

    public function all()
    {
        $lotTypes = $this->select($this->table_name);
        return $lotTypes;
    }

    public function find($id)
    {
        $data = $this->select($this->table_name, '*', [$this->pk => $id]);
        return $data[0] ?? [];
    }

    public function remove($id)
    {
        return $this->delete($this->table_name, [$this->pk => $id]);
    }

    public function add($form, $returnId = false)
    {
        return $this->insert($this->table_name, $form, $this->pk, $returnId);
    }

    public function edit($form, $id)
    {
        return $this->update($this->table_name, $form, [$this->pk => $id]);
    }

    public static function name($id)
    {
        $self = new static;
        $data = $self->find($id);
        return count($data) > 0 ? $data['name'] : '';
    }

    public static function data($id, $fld = 'name')
    {
        $self = new static;
        $data = $self->find($id);
        return count($data) > 0 ? $data[$fld] : '';
    }
}
