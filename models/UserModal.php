<?php
class UserModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($password, $row['password'])) {
            return $row;
        }
        return false;
    }

    // ✅ Don't return password hashes unnecessarily
    public function getAll() {
        return $this->db->query("SELECT user_id, fullname, email, phonenumber, role, created_at FROM Users")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Create user (hash plain password)
    public function create($fullname, $email, $phonenumber, $password, $role) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO Users (fullname, email, phonenumber, password, role)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([$fullname, $email, $phonenumber, $hashed_password, $role]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return false; // duplicate email
            }
            throw $e;
        }
    }

    // ✅ Optional helper: insert user with an already-hashed password (useful for seeding admin)
    public function createWithHash($fullname, $email, $phonenumber, $hashed_password, $role) {
        $sql = "INSERT INTO Users (fullname, email, phonenumber, password, role)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullname, $email, $phonenumber, $hashed_password, $role]);
    }

    public function update($id, $fullname, $email, $phonenumber, $role) {
        $sql = "UPDATE Users
                SET fullname = ?, email = ?, phonenumber = ?, role = ?
                WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullname, $email, $phonenumber, $role, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Users WHERE user_id = ?");
        return $stmt->execute([$id]);
    }

    public function search($query) {
        $stmt = $this->db->prepare("
            SELECT user_id, fullname, email, phonenumber, role, created_at
            FROM Users
            WHERE fullname LIKE ? OR email LIKE ?
        ");
        $searchQuery = "%$query%";
        $stmt->execute([$searchQuery, $searchQuery]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
