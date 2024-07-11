<?php

class Lot extends Model
{
    private $table_name = 'tbl_lots';
    public $pk = 'lot_id';

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
                'cemetery' => ['foreignKey' => 'cemetery_id', 'otherKey' => 'cemetery_id', 'table' => 'tbl_cemeteries'],
                'lot_type' => ['foreignKey' => 'lot_type_id', 'otherKey' => 'lot_type_id', 'table' => 'tbl_lot_types'],
                'block' => ['foreignKey' => 'block_id', 'otherKey' => 'block_id', 'table' => 'tbl_cemetery_blocks'],
            ],
            'hasMany' => [
                // 'actions' => ['foreignKey' => 'obstruction_id', 'table' => 'tbl_obstruction_actions']
            ]
        ];
    }
}
