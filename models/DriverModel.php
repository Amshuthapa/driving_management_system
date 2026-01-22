<?php

class DriverModel {

    private $db;

    // FIX 1: Removed backslash before __construct
    public function __construct($pdo) { 
        $this->db = $pdo; 
    }

    public function getAll() {
        // FIX 2: Removed backslash before *
        return $this->db->query("SELECT * FROM drivers")->fetchAll();
    }

    public function create($name, $license, $v_type) {
        $stmt = $this->db->prepare("INSERT INTO drivers (name, license_number, vehicle_type) VALUES (?, ?, ?)");
        // FIX 3: Removed backslashes from array brackets
        return $stmt->execute([$name, $license, $v_type]);
    }

    public function update($name, $license, $v_type) {
        $stmt = $this->db->prepare("UPDATE INTO drivers (name, lisence_number, veichel_type) VALUES (?,?,?)");
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM drivers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search($query) {
        $stmt = $this->db->prepare("SELECT * FROM drivers WHERE name LIKE ?");
        // FIX 4: Corrected variable interpolation in array
        $stmt->execute(["%$query%"]);
        return $stmt->fetchAll();
    }
}
?>
