<?php
include 'connection.php';

// Get the JSON input from the request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if activeYear is set in the input data
if (isset($data['activeYear'])) {
    $activeYear = $data['activeYear'];

    // Update the session variable
    $_SESSION['activeYear'] = $activeYear;
    $_SESSION['kasaDB'] = $_SESSION['kasaDBPrefix'] . $activeYear;

    // Return a JSON response indicating success
    echo json_encode(['success' => true]);
} else {
    // Return a JSON response indicating failure
    echo json_encode(['success' => false, 'message' => 'Active year not provided']);
}
?>