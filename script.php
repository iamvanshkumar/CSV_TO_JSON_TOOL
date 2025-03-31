<?php
include 'conn.php'; 

// Set header to return JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Fetch Award ID
    if (isset($_POST['fetch-awardID'])) {

        $sql = "SELECT awardID FROM award_id ORDER BY id DESC LIMIT 1";  
        $result = $db->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['awardID' => $row['awardID']]);
        } else {
            echo json_encode(['error' => 'No award ID found']);
        }

    } 
    
    // Insert Award ID
    elseif (isset($_POST['insert-awardID'])) {

        // Sanitize and validate input parameters
        $awardID = $_POST['Aid'] ?? null;
        $title = $_POST['title'] ?? null;
        $synopsis = $_POST['synopsis'] ?? null;

        if (!$awardID || !$title || !$synopsis) {
            echo json_encode(['error' => 'Missing parameters']);
            http_response_code(400);
            exit;
        }

        // Prepared statement to prevent SQL injection
        $stmt = $db->prepare("INSERT INTO award_id (awardID, title, synopsis) VALUES (?, ?, ?)");

        if (!$stmt) {
            echo json_encode(['error' => "Prepare failed: " . $db->error]);
            exit;
        }

        $stmt->bind_param("iss", $awardID, $title, $synopsis);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Award details inserted successfully']);
        } else {
            echo json_encode(['error' => "Error inserting award details: " . $stmt->error]);
        }

        $stmt->close();

    } else {
        // Invalid request
        echo json_encode(['error' => 'Invalid request']);
        http_response_code(400);  
    }

} else {
    echo json_encode(['error' => 'Invalid request method']);
    http_response_code(405);  
}
?>
