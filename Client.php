<?php

class Client extends Model
{
    private $table_name = 'tbl_clients';
    public $pk = 'client_id';

    public function all()
    {
        $clients = $this->select($this->table_name);
        return $clients;
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
}
