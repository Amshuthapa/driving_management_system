<?php
class StudentModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // List all students with assignment info (NO StudentAssignments table)
public function getAllStudents() {
    $sql = "
        SELECT 
            s.student_id,
            u.fullname,
            u.email,
            u.phonenumber,
            s.assigned_at,

            v.vehicle_no,
            v.vehicle_model,

            du.fullname AS driver_name

        FROM Students s
        JOIN Users u ON s.user_id = u.user_id

        LEFT JOIN Vehicles v 
            ON s.vehicle_assigned_id = v.vehicle_id

        LEFT JOIN Drivers d 
            ON s.driver_assigned_id = d.driver_id

        LEFT JOIN Users du 
            ON d.user_id = du.user_id

        ORDER BY s.student_id DESC
    ";

    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}



    // Delete student + user
    public function deleteStudent($student_id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT user_id FROM Students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $user_id = $stmt->fetchColumn();

            if ($user_id) {
                $this->db->prepare("DELETE FROM Students WHERE student_id = ?")
                         ->execute([$student_id]);

                $this->db->prepare("DELETE FROM Users WHERE user_id = ?")
                         ->execute([$user_id]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Fetch single student
    public function getStudentById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                s.student_id,
                s.user_id,
                s.vehicle_assigned_id,
                s.driver_assigned_id,
                s.assigned_at,
                u.fullname,
                u.email,
                u.phonenumber
            FROM Students s
            JOIN Users u ON s.user_id = u.user_id
            WHERE s.student_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user + assignment fields (in Students table)
    public function updateStudent(
        $student_id,
        $user_id,
        $fullname,
        $email,
        $phonenumber,
        $vehicle_assigned_id,
        $driver_assigned_id,
        $assigned_at
    ) {
        try {
            $this->db->beginTransaction();

            // Update Users
            $stmt1 = $this->db->prepare(
                "UPDATE Users SET fullname = ?, email = ?, phonenumber = ? WHERE user_id = ?"
            );
            $stmt1->execute([$fullname, $email, $phonenumber, $user_id]);

            // Update Students assignment fields
            $stmt2 = $this->db->prepare("
                UPDATE Students 
                SET 
                    vehicle_assigned_id = ?, 
                    driver_assigned_id = ?, 
                    assigned_at = ?
                WHERE student_id = ?
            ");
            $stmt2->execute([
                $vehicle_assigned_id,
                $driver_assigned_id,
                $assigned_at,
                $student_id
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Create student role entry
    public function createStudentEntry($user_id) {
        $stmt = $this->db->prepare("INSERT INTO Students (user_id) VALUES (?)");
        return $stmt->execute([$user_id]);
    }

    // Set assignment (optional helper)
public function assignStudent(
    $student_id,
    $vehicle_assigned_id,
    $driver_assigned_id
) {
    $stmt = $this->db->prepare("
        UPDATE Students
        SET 
            vehicle_assigned_id = ?, 
            driver_assigned_id = ?, 
            assigned_at = NOW()
        WHERE student_id = ?
    ");

    return $stmt->execute([
        $vehicle_assigned_id,
        $driver_assigned_id,
        $student_id
    ]);
}

    
}
