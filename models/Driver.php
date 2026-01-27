<?php
class DriverModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // List all drivers with user info
    public function getAllDrivers() {
        $sql = "
            SELECT
                d.driver_id,
                d.user_id,
                d.is_active,
                u.fullname,
                u.email,
                u.phonenumber
            FROM Drivers d
            JOIN Users u ON d.user_id = u.user_id
            ORDER BY d.driver_id DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create driver entry (after creating a user with role='driver')
    public function createDriverEntry($user_id, $is_active = 1) {
        $stmt = $this->db->prepare("INSERT INTO Drivers (user_id, is_active) VALUES (?, ?)");
        return $stmt->execute([$user_id, (int)$is_active]);
    }

    // Fetch single driver
    public function getDriverById($driver_id) {
        $stmt = $this->db->prepare("
            SELECT
                d.driver_id,
                d.user_id,
                d.is_active,
                u.fullname,
                u.email,
                u.phonenumber
            FROM Drivers d
            JOIN Users u ON d.user_id = u.user_id
            WHERE d.driver_id = ?
            LIMIT 1
        ");
        $stmt->execute([$driver_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update driver active status
    public function setDriverActive($driver_id, $is_active) {
        $stmt = $this->db->prepare("UPDATE Drivers SET is_active = ? WHERE driver_id = ?");
        return $stmt->execute([(int)$is_active, $driver_id]);
    }

    // Delete driver + user (like your student delete style)
    public function deleteDriver($driver_id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT user_id FROM Drivers WHERE driver_id = ?");
            $stmt->execute([$driver_id]);
            $user_id = $stmt->fetchColumn();

            if ($user_id) {
                $this->db->prepare("DELETE FROM Drivers WHERE driver_id = ?")->execute([$driver_id]);
                $this->db->prepare("DELETE FROM Users WHERE user_id = ?")->execute([$user_id]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // For dropdowns: active drivers only
    public function getActiveDrivers() {
        $stmt = $this->db->query("
            SELECT d.driver_id, u.fullname
            FROM Drivers d
            JOIN Users u ON d.user_id = u.user_id
            WHERE d.is_active = 1
            ORDER BY u.fullname ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
