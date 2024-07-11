<?php

class User extends Model
{
    private $table_name = 'tbl_users';
    private $pk = 'user_id';

    public function all()
    {
        $users = $this->select($this->table_name);
        return $users;
    }

    public function usernameExists($username)
    {
        $data = $this->select($this->table_name, '*', ['username' => $username]);
        return $data;
    }

    public function register($form, $returnId = false)
    {
        $form['password'] = password_hash($form['password'], PASSWORD_BCRYPT);
        return $this->insert($this->table_name, $form, $this->pk, $returnId);
    }

    public function login($form)
    {
        $user = $this->select($this->table_name, '*', ['username' => $form['username']]);

        if ($user && password_verify($form['password'], $user[0]['password'])) {
            $_SESSION[SYSTEM] = $user[0];
            return true;
        } else {
            return false;
        }
    }
}
