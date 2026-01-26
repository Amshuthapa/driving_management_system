<?php
class VehicleModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Get all vehicles for the table list
    public function getAllVehicles() {
        return $this->db->query("SELECT * FROM Vehicles ORDER BY vehicle_id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new vehicle record
    public function createVehicle($type, $model, $number) {
        $stmt = $this->db->prepare("INSERT INTO Vehicles (vehicle_type, vehicle_model, vehicle_no) VALUES (?, ?, ?)");
        return $stmt->execute([$type, $model, $number]);
    }

    // Find a specific vehicle (useful for assignments)
    public function getVehicleById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Vehicles WHERE vehicle_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Delete a vehicle
    public function deleteVehicle($id) {
        $stmt = $this->db->prepare("DELETE FROM Vehicles WHERE vehicle_id = ?");
        return $stmt->execute([$id]);
    }
}