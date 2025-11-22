<?php
// User model class representing the user entity and containing methods for user-related database operations

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($username, $password, $fullName, $role) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $passwordHash, $fullName, $role]);
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $fullName, $role) {
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, role = ? WHERE id = ?");
        return $stmt->execute([$fullName, $role, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>