<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $reference_no = $_GET['reference_no'] ?? '';

        if (empty($reference_no)) {
            throw new Exception("Reference number is required");
        }

        // Check if reference number exists
        $sql = "SELECT id FROM payments WHERE reference_no = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }

        $stmt->bind_param("s", $reference_no);
        $stmt->execute();
        $result = $stmt->get_result();

        echo json_encode([
            "exists" => $result->num_rows > 0
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "error" => true,
            "message" => $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    echo json_encode([
        "error" => true,
        "message" => "Invalid request method"
    ]);
}
?> 