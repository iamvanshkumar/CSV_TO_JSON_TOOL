<?php
include 'conn.php'; 

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

        $awardID = $_POST['Aid'];
        $title = $_POST['title'] ?? NULL;
        $synopsis = $_POST['synopsis'] ?? NULL;
        // Prepared statement to avoid SQL injection
        $stmt = $db->prepare("INSERT INTO award_id (awardID, title, synopsis) 
                              VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param("issss", $awardID, $title, $synopsis);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Award details inserted successfully']);
        } else {
            echo json_encode(['error' => "Error inserting award details: {$db->error}"]);
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
