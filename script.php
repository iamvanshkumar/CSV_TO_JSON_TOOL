<?php
include 'conn.php';

// Set header to return JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    http_response_code(405);
    exit;
}

// Function to respond with JSON and handle errors
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

try {
    // Check for duplicate title and synopsis
    if (isset($_POST['check-duplicate'])) {
        $title = $_POST['title'] ?? null;
        $synopsis = $_POST['synopsis'] ?? null;

        if (!$title || !$synopsis) {
            jsonResponse(['error' => 'Missing parameters'], 400);
        }

        $stmt = $db->prepare("SELECT awardID FROM award_id WHERE title = ? AND synopsis = ?");
        $stmt->bind_param("ss", $title, $synopsis);
        $stmt->execute();
        $result = $stmt->get_result();

        jsonResponse($result->num_rows > 0 ? ['exists' => true, 'awardID' => $result->fetch_assoc()['awardID']] : ['exists' => false]);

    }

    // Fetch the latest baseID
    elseif (isset($_POST['fetch-baseID'])) {
        $sql = "SELECT baseID FROM award_id ORDER BY id DESC LIMIT 1";
        $result = $db->query($sql);

        jsonResponse($result && $result->num_rows > 0
            ? ['baseID' => $result->fetch_assoc()['baseID']]
            : ['baseID' => 1]);

    }

    // Check if AwardID already exists
    elseif (isset($_POST['check-awardID'])) {
        $awardID = intval($_POST['awardID']);

        $stmt = $db->prepare("SELECT COUNT(*) AS count FROM award_id WHERE awardID = ?");
        $stmt->bind_param("i", $awardID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        jsonResponse(['exists' => $result['count'] > 0]);

    }

    // Save new BaseID, AwardID, Title, and Synopsis together
    elseif (isset($_POST['save-baseID'])) {
        $newBaseID = intval($_POST['baseID']);
        $awardID = intval($_POST['awardID']);
        $title = $_POST['title'] ?? null;
        $synopsis = $_POST['synopsis'] ?? null;
    
        if (!$newBaseID || !$awardID || !$title || !$synopsis) {
            echo json_encode(['error' => 'Missing parameters']);
            http_response_code(400);
            exit;
        }
    
        // Use INSERT IGNORE to prevent duplicates or update existing row
        $stmt = $db->prepare("
            INSERT INTO award_id (baseID, awardID, title, synopsis) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE awardID = VALUES(awardID)
        ");
    
        if (!$stmt) {
            echo json_encode(['error' => "Prepare failed: " . $db->error]);
            exit;
        }
    
        $stmt->bind_param("iiss", $newBaseID, $awardID, $title, $synopsis);
    
        if ($stmt->execute()) {
            echo json_encode(['success' => 'BaseID and AwardID inserted/updated successfully']);
        } else {
            echo json_encode(['error' => 'Failed to insert BaseID and AwardID']);
        }
    
        $stmt->close();
    } else {
        jsonResponse(['error' => 'Invalid request'], 400);
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
