<?php

class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function beginTransaction()
    {
        $this->db->begin_transaction();
    }

    public function commitTransaction()
    {
        $this->db->commit();
    }

    public function rollbackTransaction()
    {
        $this->db->rollback();
    }

    public function query($sql)
    {
        return $this->db->query($sql);
    }

    public function prepare($sql)
    {
        return $this->db->prepare($sql);
    }

    public function generateUUIDv4()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set variant to 10xx
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Insert data into a table
    public function insert($table, $data, $pk = 'id', $returnId = false)
    {
        $data[$pk] = $this->generateUUIDv4();
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $stmt = $this->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));

        if ($stmt->execute()) {
            return $returnId ? $data[$pk] : true;
        }
        return false;
    }

    // Update data in a table
    public function update($table, $data, $where)
    {
        $setClause = implode(', ', array_map(fn ($key) => "$key = ?", array_keys($data)));
        $whereClause = implode(' AND ', array_map(fn ($key) => "$key = ?", array_keys($where)));
        $sql = "UPDATE $table SET $setClause WHERE $whereClause";

        $stmt = $this->prepare($sql);
        $params = array_merge(array_values($data), array_values($where));
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);

        return $stmt->execute();
    }

    // Delete data from a table
    public function delete($table, $where)
    {
        $whereClause = implode(' AND ', array_map(fn ($key) => "$key = ?", array_keys($where)));
        $sql = "DELETE FROM $table WHERE $whereClause";

        $stmt = $this->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($where)), ...array_values($where));

        return $stmt->execute();
    }
    // Select data from a table with relationships
    public function select($table, $columns = '*', $where = [], $orderBy = [], $with = [])
    {
        $whereClause = $where ? 'WHERE ' . implode(' AND ', array_map(fn ($key) => "$key = ?", array_keys($where))) : '';
        $orderByClause = '';

        if (!empty($orderBy)) {
            $orderByParts = [];
            foreach ($orderBy as $column => $direction) {
                $orderByParts[] = "$column $direction";
            }
            $orderByClause = 'ORDER BY ' . implode(', ', $orderByParts);
        }

        $sql = "SELECT $columns FROM $table $whereClause $orderByClause";

        // Process relationships
        $results = $this->executeSelect($sql, $where);
        if (!empty($with)) {
            $relationships = $this->relationships();
            foreach ($with as $relation) {
                if (isset($relationships['belongsTo'][$relation])) {
                    $results = $this->loadBelongsTo($results, $relationships['belongsTo'][$relation], $relation);
                }

                if (isset($relationships['hasMany'][$relation])) {
                    $results = $this->loadHasMany($results, $relationships['hasMany'][$relation], $relation);
                }
            }
        }

        return $results;
    }

    // Utility method to execute SELECT queries and fetch results
    protected function executeSelect($sql, $where)
    {
        $stmt = $this->prepare($sql);
        if ($where) {
            $stmt->bind_param(str_repeat('s', count($where)), ...array_values($where));
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Load belongsTo relationships
    protected function loadBelongsTo($results, $relationship, $relationName)
    {
        $foreignKey = $relationship['foreignKey'];
        $otherKey = $relationship['otherKey'];
        $relatedTable = $relationship['table'];
        $relatedIds = array_column($results, $foreignKey);

        if (empty($relatedIds)) {
            return $results;
        }

        $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
        $sql = "SELECT * FROM $relatedTable WHERE $otherKey IN ($placeholders)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($relatedIds)), ...$relatedIds);
        $stmt->execute();
        $relatedResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $relatedMap = [];
        foreach ($relatedResults as $row) {
            $relatedMap[$row[$otherKey]] = $row;
        }

        foreach ($results as &$result) {
            $relatedId = $result[$foreignKey];
            $result[$relationName] = $relatedMap[$relatedId] ?? null;
        }

        return $results;
    }

    // Load hasMany relationships
    protected function loadHasMany($results, $relationship, $relationName)
    {
        $foreignKey = $relationship['foreignKey'];
        $relatedTable = $relationship['table'];
        $primaryKey = array_key_exists('primaryKey', $relationship) ? $relationship['primaryKey'] : $this->pk;
        $primaryKeys = array_column($results, $primaryKey);

        if (empty($primaryKeys)) {
            return $results;
        }

        $placeholders = implode(',', array_fill(0, count($primaryKeys), '?'));
        $sql = "SELECT * FROM $relatedTable WHERE $foreignKey IN ($placeholders)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($primaryKeys)), ...$primaryKeys);
        $stmt->execute();
        $relatedResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $relatedMap = [];
        foreach ($relatedResults as $row) {
            $relatedMap[$row[$foreignKey]][] = $row;
        }

        foreach ($results as &$result) {
            $relatedId = $result[$primaryKey];
            $result[$relationName] = $relatedMap[$relatedId] ?? [];
        }

        return $results;
    }

    // Define relationships (empty by default, override in subclasses)
    public function relationships()
    {
        return [];
    }

    // Utility method to get table columnss
    private function getTableColumns($table)
    {
        $columns = [];
        $result = $this->query("SHOW COLUMNS FROM $table");
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        return $columns;
    }

    public function __destruct()
    {
        $this->db->close();
    }
}
