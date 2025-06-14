<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get form data
        $id = $_POST['id'] ?? null;
        $school_name = $_POST['school_name'] ?? '';
        $sanction_no = $_POST['sanction_no'] ?? '';
        $udise = $_POST['udise'] ?? '';
        $fund_date = $_POST['fund_date'] ?? NULL;
        $inauguration_date = $_POST['inauguration_date'] ?? NULL;
        $principal_name_phone = $_POST['principal_name_phone'] ?? '';
        $atl_code = $_POST['atl_code'] ?? '';
        $atl_pswd = $_POST['atl_pswd'] ?? '';
        $gem_code = $_POST['gem_code'] ?? '';
        $govt_email = $_POST['govt_email'] ?? '';
        $pfms = $_POST['pfms'] ?? '';
        $pfms_op = $_POST['pfms_op'] ?? '';
        $pfms_ap = $_POST['pfms_ap'] ?? '';
        $atl_incharge_phone = $_POST['atl_incharge_phone'] ?? '';
        $dashbrd_pswd = $_POST['dashbrd_pswd'] ?? '';
        $gem_pswd = $_POST['gem_pswd'] ?? '';
        $govt_mail_pswd = $_POST['govt_mail_pswd'] ?? '';
        $pfms_pswd = $_POST['pfms_pswd'] ?? '';
        $op_pswd = $_POST['op_pswd'] ?? '';
        $ap_pswd = $_POST['ap_pswd'] ?? '';
        $reg_email = $_POST['reg_email'] ?? '';
        $reg_phone = $_POST['reg_phone'] ?? '';
        $bharatkosh = $_POST['bharatkosh'] ?? '';
        $bkosh_password = $_POST['bkosh_password'] ?? '';
        $atl_vendor = $_POST['atl_vendor'] ?? '';
        $vcomment = $_POST['vcomment'] ?? '';

        // Validation for required fields for both insert and update
        $errors = [];
        if (empty($school_name)) $errors[] = "School Name is required.";
        if (empty($atl_code)) $errors[] = "ATL Code is required.";

        // Add more required fields here as needed, e.g., if (empty($sanction_no)) $errors[] = "Sanction No is required.";

        if (!empty($errors)) {
            throw new Exception(implode(" ", $errors));
        }

        // Check for duplicate ATL code
        if ($id) {
            $check_sql = "SELECT id FROM atl_details WHERE atl_code = ? AND id != ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("si", $atl_code, $id);
        } else {
            $check_sql = "SELECT id FROM atl_details WHERE atl_code = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $atl_code);
        }

        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            throw new Exception("ATL Code already exists. Duplicate ATL codes are not allowed.");
        }
        $check_stmt->close();

        if ($id) {
            // Update existing record
            $sql = "UPDATE atl_details SET
                school_name = ?, sanction_no = ?, udise = ?, fund_date = ?, inauguration_date = ?,
                principal_name_phone = ?, atl_code = ?, atl_pswd = ?, gem_code = ?, govt_email = ?,
                pfms = ?, pfms_op = ?, pfms_ap = ?, atl_incharge_phone = ?, dashbrd_pswd = ?,
                gem_pswd = ?, govt_mail_pswd = ?, pfms_pswd = ?, op_pswd = ?, ap_pswd = ?,
                reg_email = ?, reg_phone = ?, bharatkosh = ?, bkosh_password = ?, atl_vendor = ?, vcomment = ?
                WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $conn->error);
            }

            $stmt->bind_param("ssssssssssssssssssssssssssi", 
                $school_name, $sanction_no, $udise, $fund_date, $inauguration_date,
                $principal_name_phone, $atl_code, $atl_pswd, $gem_code, $govt_email,
                $pfms, $pfms_op, $pfms_ap, $atl_incharge_phone, $dashbrd_pswd,
                $gem_pswd, $govt_mail_pswd, $pfms_pswd, $op_pswd, $ap_pswd,
                $reg_email, $reg_phone, $bharatkosh, $bkosh_password, $atl_vendor, $vcomment,
                $id
            );
            $message = "ATL details updated successfully!";
        } else {
            // Insert new record
            $sql = "INSERT INTO atl_details (
                school_name, sanction_no, udise, fund_date, inauguration_date,
                principal_name_phone, atl_code, atl_pswd, gem_code, govt_email,
                pfms, pfms_op, pfms_ap, atl_incharge_phone, dashbrd_pswd,
                gem_pswd, govt_mail_pswd, pfms_pswd, op_pswd, ap_pswd,
                reg_email, reg_phone, bharatkosh, bkosh_password, atl_vendor, vcomment
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $conn->error);
            }

            $stmt->bind_param("ssssssssssssssssssssssssss", 
                $school_name, $sanction_no, $udise, $fund_date, $inauguration_date,
                $principal_name_phone, $atl_code, $atl_pswd, $gem_code, $govt_email,
                $pfms, $pfms_op, $pfms_ap, $atl_incharge_phone, $dashbrd_pswd,
                $gem_pswd, $govt_mail_pswd, $pfms_pswd, $op_pswd, $ap_pswd,
                $reg_email, $reg_phone, $bharatkosh, $bkosh_password, $atl_vendor, $vcomment
            );
            $message = "ATL details added successfully!";
        }

        if (!$stmt->execute()) {
            throw new Exception("Database execute error: " . $stmt->error);
        }

        echo json_encode([
            "status" => "success",
            "message" => $message
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Error: " . $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
}
?>
