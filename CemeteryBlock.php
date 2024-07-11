<?php

class CemeteryBlock extends Model
{
    private $table_name = 'tbl_cemetery_blocks';
    private $pk = 'block_id';

    public function all()
    {
        $obstruction_types = $this->select($this->table_name);
        return $obstruction_types;
    }

    public function find($id)
    {
        $obstruction_type = $this->select($this->table_name, '*', [$this->pk => $id]);
        return $obstruction_type[0] ?? [];
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

    public static function data($id, $fld = 'name')
    {
        $self = new static;
        $data = $self->find($id);
        return count($data) > 0 ? $data[$fld] : '';
    }
}
