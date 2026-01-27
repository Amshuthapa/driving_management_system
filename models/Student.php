<?php
class StudentModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Admin: List all students with booking + assignment info
     */
    public function getAllStudents() {
        $sql = "
            SELECT 
                s.student_id,
                s.user_id,

                u.fullname,
                u.email,
                u.phonenumber,

                s.requested_date,
                s.requested_time,
                s.booking_status,

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

    /**
     * Delete student + user
     */
    public function deleteStudent($student_id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT user_id FROM Students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $user_id = $stmt->fetchColumn();

            if ($user_id) {
                $this->db->prepare("DELETE FROM Students WHERE student_id = ?")->execute([$student_id]);
                $this->db->prepare("DELETE FROM Users WHERE user_id = ?")->execute([$user_id]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Fetch single student (admin edit)
     */
    public function getStudentById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                s.student_id,
                s.user_id,

                s.requested_date,
                s.requested_time,
                s.booking_status,

                s.vehicle_assigned_id,
                s.driver_assigned_id,
                s.assigned_at,

                u.fullname,
                u.email,
                u.phonenumber
            FROM Students s
            JOIN Users u ON s.user_id = u.user_id
            WHERE s.student_id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update user basic info (admin)
     * Keep booking/assignment unchanged unless specifically updated elsewhere
     */
    public function updateStudentUserInfo($student_id, $user_id, $fullname, $email, $phonenumber) {
        try {
            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare("
                UPDATE Users 
                SET fullname = ?, email = ?, phonenumber = ? 
                WHERE user_id = ?
            ");
            $stmt1->execute([$fullname, $email, $phonenumber, $user_id]);

            // If you want to ensure student exists:
            $stmt2 = $this->db->prepare("UPDATE Students SET student_id = student_id WHERE student_id = ?");
            $stmt2->execute([$student_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Create student entry (after Users insert with role='student')
     */
    public function createStudentEntry($user_id) {
        $stmt = $this->db->prepare("
            INSERT INTO Students (user_id, booking_status) 
            VALUES (?, 'none')
        ");
        return $stmt->execute([$user_id]);
    }

    /**
     * Student: request a booking slot (date+time) -> status requested
     * Admin will assign driver + vehicle later
     */
    public function requestBooking($user_id, $requested_date, $requested_time) {
        $stmt = $this->db->prepare("
            UPDATE Students
            SET
                requested_date = ?,
                requested_time = ?,
                booking_status = 'requested',
                driver_assigned_id = NULL,
                vehicle_assigned_id = NULL,
                assigned_at = NULL
            WHERE user_id = ?
        ");
        return $stmt->execute([$requested_date, $requested_time, $user_id]);
    }

    /**
     * Student: cancel their booking request (or assigned booking)
     */
    public function cancelBooking($user_id) {
        $stmt = $this->db->prepare("
            UPDATE Students
            SET
                booking_status = 'cancelled'
            WHERE user_id = ?
        ");
        return $stmt->execute([$user_id]);
    }

    /**
     * Admin: Assign driver+vehicle ONLY if student has requested slot
     */
    public function assignStudent($student_id, $vehicle_assigned_id, $driver_assigned_id) {
        $stmt = $this->db->prepare("
            UPDATE Students
            SET 
                vehicle_assigned_id = ?, 
                driver_assigned_id = ?, 
                assigned_at = NOW(),
                booking_status = 'assigned'
            WHERE student_id = ?
        ");
        return $stmt->execute([$vehicle_assigned_id, $driver_assigned_id, $student_id]);
    }

    /**
     * For booking availability checks:
     * Count available active drivers for a slot (date+time)
     */
    public function countAvailableDrivers($date, $time) {
        $sql = "
            SELECT COUNT(*) 
            FROM Drivers
            WHERE is_active = 1
            AND driver_id NOT IN (
                SELECT driver_assigned_id
                FROM Students
                WHERE requested_date = ? AND requested_time = ?
                AND booking_status = 'assigned'
                AND driver_assigned_id IS NOT NULL
            )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$date, $time]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count available active vehicles for a slot (date+time)
     */
    public function countAvailableVehicles($date, $time) {
        $sql = "
            SELECT COUNT(*)
            FROM Vehicles
            WHERE is_active = 1
            AND vehicle_id NOT IN (
                SELECT vehicle_assigned_id
                FROM Students
                WHERE requested_date = ? AND requested_time = ?
                AND booking_status = 'assigned'
                AND vehicle_assigned_id IS NOT NULL
            )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$date, $time]);
        return (int)$stmt->fetchColumn();
    }
    public function requestBookingByStudentId($student_id, $requested_date, $requested_time) {
    $stmt = $this->db->prepare("
        UPDATE Students
        SET
            requested_date = ?,
            requested_time = ?,
            booking_status = 'requested',
            driver_assigned_id = NULL,
            vehicle_assigned_id = NULL,
            assigned_at = NULL
        WHERE student_id = ?
    ");
    return $stmt->execute([$requested_date, $requested_time, $student_id]);
}

}
