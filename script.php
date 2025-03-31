<?php
include 'conn.php';  // Ensure this connects to your MySQL database

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch-awardID'])) {

    // SQL query to fetch the latest award ID
    $sql = "SELECT awardID FROM award_id ORDER BY id DESC LIMIT 1";  // Use your primary key column
    $result = $db->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['awardID' => $row['awardID']]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'No award ID found']);
    }

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}

// Close the database connection
$db->close();
?>
