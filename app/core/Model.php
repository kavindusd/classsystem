<?php

abstract class Model {
    protected PDO $db;
    protected string $table = '';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function findById(int $id): array|false {
        return $this->query("SELECT * FROM {$this->table} WHERE id = ?", [$id])->fetch();
    }

    public function findAll(): array {
        return $this->query("SELECT * FROM {$this->table}")->fetchAll();
    }

    public function insert(array $data): int {
        $cols   = implode(', ', array_map(fn($k) => "`{$k}`", array_keys($data)));
        $places = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO {$this->table} ({$cols}) VALUES ({$places})", array_values($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $set  = implode(', ', array_map(fn($k) => "`{$k}` = ?", array_keys($data)));
        $vals = array_values($data);
        $vals[] = $id;
        $this->query("UPDATE {$this->table} SET {$set} WHERE id = ?", $vals);
        return true;
    }

    public function delete(int $id): bool {
        $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
        return true;
    }
}
