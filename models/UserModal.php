<?php
class UserModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Login updated to use 'email' instead of 'username'
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM Users")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create updated with new fields: fullname, email, phonenumber
public function create($fullname, $email, $phonenumber, $password, $role) {
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Users (fullname, email, phonenumber, password, role)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullname, $email, $phonenumber, $hashed_password, $role]);

    } catch (PDOException $e) {
        // Duplicate email (unique constraint violation)
        if ($e->getCode() == 23000) {
            return false;
        }

        throw $e; // rethrow unexpected DB errors
    }
}


    // Update updated to include phonenumber and fullname
    public function update($id, $fullname, $email, $phonenumber, $role) {
        $sql = "UPDATE Users SET fullname = ?, email = ?, phonenumber = ?, role = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullname, $email, $phonenumber, $role, $id]);
    }

    // Delete uses 'user_id' as the primary key
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Users WHERE user_id = ?");
        return $stmt->execute([$id]);
    }

    // Search updated to search by fullname or email
    public function search($query) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE fullname LIKE ? OR email LIKE ?");
        $searchQuery = "%$query%";
        $stmt->execute([$searchQuery, $searchQuery]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>