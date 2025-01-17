<?php

class UserRepository {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function findById($id) {
        $query = "SELECT * FROM users WHERE id = :id";
        return $this->db->query($query, ['id' => $id])->fetch();
    }

    public function findByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        return $this->db->query($query, ['email' => $email])->fetch();
    }

    public function create($user) {
        $query = "INSERT INTO users (name, email, password, role, created_at, updated_at) 
                  VALUES (:name, :email, :password, :role, :created_at, :updated_at)";
        $this->db->query($query, $user);
        return $this->db->lastInsertId();
    }

    public function update($user) {
        $query = "UPDATE users 
                  SET name = :name, email = :email, password = :password, 
                      role = :role, updated_at = :updated_at 
                  WHERE id = :id";
        return $this->db->query($query, $user)->rowCount();
    }

    public function delete($id) {
        $query = "DELETE FROM users WHERE id = :id";
        return $this->db->query($query, ['id' => $id])->rowCount();
    }
}