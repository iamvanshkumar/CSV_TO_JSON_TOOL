<?php
include 'conn.php'; 

// Set header to return JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check for duplicate title and synopsis
    if (isset($_POST['check-duplicate'])) {
        $title = $_POST['title'] ?? null;
        $synopsis = $_POST['synopsis'] ?? null;

        if ($title && $synopsis) {
            // Check if the combination already exists
            $stmt = $db->prepare("SELECT awardID FROM award_id WHERE title = ? AND synopsis = ?");
            $stmt->bind_param("ss", $title, $synopsis);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode(['exists' => true, 'awardID' => $row['awardID']]);
            } else {
                echo json_encode(['exists' => false]);
            }

            $stmt->close();
        } else {
            echo json_encode(['error' => 'Missing parameters']);
            http_response_code(400);
        }
    }

    // Fetch the latest baseID
    elseif (isset($_POST['fetch-baseID'])) {
        $sql = "SELECT baseID FROM award_id ORDER BY id DESC LIMIT 1";  
        $result = $db->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['baseID' => $row['baseID']]);
        } else {
            echo json_encode(['baseID' => 1]);  // Initialize baseID if table is empty
        }
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

        // Insert the new baseID, awardID, title, and synopsis into the table
        $stmt = $db->prepare("INSERT INTO award_id (baseID, awardID, title, synopsis) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            echo json_encode(['error' => "Prepare failed: " . $db->error]);
            exit;
        }

        $stmt->bind_param("iiss", $newBaseID, $awardID, $title, $synopsis);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'BaseID and AwardID inserted successfully']);
        } else {
            echo json_encode(['error' => 'Failed to insert BaseID and AwardID']);
        }

        $stmt->close();
    }

    else {
        echo json_encode(['error' => 'Invalid request']);
        http_response_code(400);
    }

} else {
    echo json_encode(['error' => 'Invalid request method']);
    http_response_code(405);
}
?>
