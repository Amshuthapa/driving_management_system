<?php
class DriverModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Purely fetch driver/tutor information
    public function getAllDrivers() {
        $sql = "SELECT d.driver_id, u.user_id, u.fullname, u.email, u.phonenumber 
                FROM Drivers d
                JOIN Users u ON d.user_id = u.user_id
                ORDER BY d.driver_id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete both the driver record and the associated user account
    public function deleteDriver($driver_id) {
        // First get the user_id to delete from Users table too
        $stmt = $this->db->prepare("SELECT user_id FROM Drivers WHERE driver_id = ?");
        $stmt->execute([$driver_id]);
        $user_id = $stmt->fetchColumn();

        if ($user_id) {
            // Delete from Drivers (Child) then Users (Parent)
            $this->db->prepare("DELETE FROM Drivers WHERE driver_id = ?")->execute([$driver_id]);
            return $this->db->prepare("DELETE FROM Users WHERE user_id = ?")->execute([$user_id]);
        }
        return false;
    }
    
/**
 * Fetch a single driver by ID with user details for editing
 */
public function getDriverById($id) {
    $stmt = $this->db->prepare("
        SELECT d.driver_id, u.user_id, u.fullname, u.email, u.phonenumber 
        FROM Drivers d
        JOIN Users u ON d.user_id = u.user_id
        WHERE d.driver_id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Update both User and Driver information
 */
public function updateDriver($driver_id, $user_id, $fullname, $email, $phonenumber) {
    try {
        $this->db->beginTransaction();

        // 1. Update the Users table (Identity)
        $stmt1 = $this->db->prepare("UPDATE Users SET fullname = ?, email = ?, phonenumber = ? WHERE user_id = ?");
        $stmt1->execute([$fullname, $email, $phonenumber, $user_id]);

        // 2. Update the Drivers table (Role-specific data if any)
        // Currently, we don't have driver-specific columns to update, but 
        // we keep this here for future fields like 'license_number'.
        
        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        return false;
    }
}
// Inside models/Driver.php

// This creates the connection between the User and the Driver role
public function createDriverEntry($user_id) {
    $stmt = $this->db->prepare("INSERT INTO Drivers (user_id) VALUES (?)");
    return $stmt->execute([$user_id]);
}
}