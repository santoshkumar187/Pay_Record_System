<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get form data
        $category = $_POST['category'] ?? '';
        $recurring_type = $_POST['recurring_type'] ?? 'Non-Recurring';
        $slab = $_POST['slab'] ?? '';
        $date = $_POST['date'] ?? date('Y-m-d');
        $amount = $_POST['amount'] ?? 0;
        $vendor = $_POST['vendor'] ?? '';
        $type = $_POST['type'] ?? 'Credit';
        $reference_no = $_POST['reference_no'] ?? '';
        $balance = $_POST['balance'] ?? 0;
        $comment = $_POST['comment'] ?? '';

        // Validate required fields
        if (empty($category) || empty($slab) || empty($vendor) || empty($reference_no)) {
            throw new Exception("Required fields cannot be empty");
        }

        // Check if reference_no already exists
        $check_sql = "SELECT id FROM payments WHERE reference_no = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $reference_no);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            throw new Exception("Reference number already exists. Please use a different one.");
        }
        $check_stmt->close();

        // Handle file upload
        $file_name = "";
        if (isset($_FILES['payment_file']) && $_FILES['payment_file']['error'] == 0) {
            $target_dir = "uploads/";
            $file_name = time() . '_' . basename($_FILES["payment_file"]["name"]);
            $target_file = $target_dir . $file_name;
            move_uploaded_file($_FILES["payment_file"]["tmp_name"], $target_file);
        }

        // Prepare and execute the SQL query
        $sql = "INSERT INTO payments (category, recurring_type, slab, date, amount, vendor, type, reference_no, balance, comment, file_name) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }

        $stmt->bind_param("ssssdsssdss", 
            $category, 
            $recurring_type, 
            $slab, 
            $date, 
            $amount, 
            $vendor, 
            $type, 
            $reference_no, 
            $balance, 
            $comment, 
            $file_name
        );

        if (!$stmt->execute()) {
            throw new Exception("Database execute error: " . $stmt->error);
        }

        // Success response
        echo json_encode([
            "status" => "success",
            "message" => "Payment record added successfully"
        ]);

    } catch (Exception $e) {
        // Error response
        echo json_encode([
            "status" => "error",
            "message" => "An error occurred while saving the payment record. Please try again. Details: " . $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
}
?>
