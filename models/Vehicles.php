<?php
class VehicleModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getAllVehicles() {
        return $this->db->query("
            SELECT vehicle_id, vehicle_no, vehicle_model
            FROM Vehicles
            ORDER BY vehicle_id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehicleById($id) {
        $stmt = $this->db->prepare("
            SELECT vehicle_id, vehicle_no, vehicle_model
            FROM Vehicles
            WHERE vehicle_id = ?
            LIMIT 1
        ");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createVehicle($vehicle_no, $vehicle_model) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Vehicles (vehicle_no, vehicle_model)
                VALUES (?, ?)
            ");
            return $stmt->execute([$vehicle_no, $vehicle_model]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) return false; // duplicate vehicle_no
            throw $e;
        }
    }

    public function updateVehicle($vehicle_id, $vehicle_no, $vehicle_model) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Vehicles
                SET vehicle_no = ?, vehicle_model = ?
                WHERE vehicle_id = ?
            ");
            return $stmt->execute([$vehicle_no, $vehicle_model, (int)$vehicle_id]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) return false; // duplicate vehicle_no
            throw $e;
        }
    }

    public function deleteVehicle($id) {
        $stmt = $this->db->prepare("DELETE FROM Vehicles WHERE vehicle_id = ?");
        return $stmt->execute([(int)$id]);
    }
}
