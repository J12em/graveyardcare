<?php

class LotPrice extends Model
{
    private $table_name = 'tbl_lot_prices';
    public $pk = 'price_id';

    public $plans = ["REGULAR" => "Regular", "PREMIUM" => "Premium", "SPECIAL" => "Special Premium"];
    public $dps = ["24" => "Fulldown (24%)", "15" => "Lowdown (15%)", "0" => "No Down"];
    public $terms = ["1" => "1 Year Term", "2" => "2 Years Term", "3" => "3 Years Term"];

    public function all()
    {
        $prices = $this->select($this->table_name);
        return $prices;
    }

    public function find($id)
    {
        $price = $this->select($this->table_name, '*', [$this->pk => $id]);
        return $price[0] ?? [];
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

    public static function data($id, $fld = 'name')
    {
        $self = new static;
        $data = $self->find($id);
        return count($data) > 0 ? $data[$fld] : '';
    }

    public static function price($lot_type_id, $plan, $percent, $term)
    {
        $self = new static;
        $price = $self->select($self->table_name, '*', [
            'lot_type_id' => $lot_type_id,
            'plan' => $plan,
            'dp_percent' => $percent,
            'term' => $term
        ]);
        return $price[0] ?? [];
    }
}
