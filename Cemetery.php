<?php

class Cemetery extends Model
{
    private $table_name = 'tbl_cemeteries';
    public $pk = 'cemetery_id';

    public function all()
    {
        $obstruction_types = $this->select($this->table_name);
        return $obstruction_types;
    }

    public function find($id, $with = [])
    {
        $obstruction_type = $this->select($this->table_name, '*', [$this->pk => $id], [], $with);
        return $obstruction_type[0] ?? [];
    }

    public function remove($id)
    {
        return $this->delete($this->table_name, [$this->pk => $id]);
    }

    public function add($form)
    {
        return $this->insert($this->table_name, $form, $this->pk);
    }

    public function edit($form, $id)
    {
        return $this->update($this->table_name, $form, [$this->pk => $id]);
    }

    // Define relationships
    public function relationships()
    {
        return [
            'belongsTo' => [
                // 'brgy' => ['foreignKey' => 'brgy_id', 'otherKey' => 'brgy_id', 'table' => 'tbl_barangays'],
            ],
            'hasMany' => [
                'blocks' => ['foreignKey' => 'cemetery_id', 'table' => 'tbl_cemetery_blocks'],
                'lots' => ['foreignKey' => 'cemetery_id', 'table' => 'tbl_lots'],
            ]
        ];
    }
}
