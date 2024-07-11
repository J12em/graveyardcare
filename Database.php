<?php

class Database extends Model
{
    public function getSomething()
    {
        $stmt = $this->prepare("SELECT * FROM tbl_users");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
