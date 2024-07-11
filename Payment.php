<?php

class Payment extends Model
{
    private $table_name = 'tbl_payments';
    public $pk = 'payment_id';

    public function all($with = [])
    {
        $payments = $this->select($this->table_name, '*', [], [], $with);
        return $payments;
    }

    public function find($id)
    {
        $client = $this->select($this->table_name, '*', [$this->pk => $id]);
        return $client[0] ?? [];
    }

    public function findByUserId($id, $with = [])
    {
        $client = $this->select($this->table_name, '*', ['user_id' => $id], [], $with);
        return $client[0] ?? [];
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
                'reservation' => ['foreignKey' => 'reservation_id', 'otherKey' => 'reservation_id', 'table' => 'tbl_reservations'],
                'client' => ['foreignKey' => 'client_id', 'otherKey' => 'client_id', 'table' => 'tbl_clients'],
            ],
            'hasMany' => [
                // 'blocks' => ['foreignKey' => 'cemetery_id', 'table' => 'tbl_cemetery_blocks'],
                // 'lots' => ['foreignKey' => 'cemetery_id', 'table' => 'tbl_lots'],
            ]
        ];
    }
}
