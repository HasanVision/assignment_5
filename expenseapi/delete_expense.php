<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'connect.php';

$expense_id = ($_GET['id'] ?? '');

if (!empty($expense_id)) {
    $expense_id = mysqli_real_escape_string($con, (int)$expense_id);

    // Fetch the record to get the image name
    $fetch_sql = "SELECT imageName FROM `expenses` WHERE `id` = '{$expense_id}' LIMIT 1";
    $result = mysqli_query($con, $fetch_sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $imageName = $row['imageName'];

        // Delete the record from the database
        $delete_sql = "DELETE FROM `expenses` WHERE `id` = '{$expense_id}' LIMIT 1";
        if (mysqli_query($con, $delete_sql)) {
            // Delete the file if it exists
            if (!empty($imageName)) {
                $filePath = __DIR__ . '/uploads/' . $imageName;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            http_response_code(204); // No Content
        } else {
            http_response_code(422); // Unprocessable Entity
            echo json_encode(['error' => 'Failed to delete expense.']);
        }
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Expense not found.']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid expense ID.']);
}
?>