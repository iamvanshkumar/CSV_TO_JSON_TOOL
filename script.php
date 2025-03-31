<?php
include 'conn.php';  // Database connection

// Set header to return JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    http_response_code(405);
    exit;
}

// Function to respond with JSON
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

try {
    // ✅ Step 1: Check for duplicate title + synopsis and return existing Award ID
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

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            jsonResponse(['exists' => true, 'awardID' => $row['awardID']]);
        } else {
            jsonResponse(['exists' => false]);
        }
    }

    // ✅ Step 2: Fetch the latest baseID
    elseif (isset($_POST['fetch-baseID'])) {
        $sql = "SELECT baseID FROM award_id ORDER BY id DESC LIMIT 1";
        $result = $db->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            jsonResponse(['baseID' => $row['baseID']]);
        } else {
            jsonResponse(['baseID' => 1]);
        }
    }

    // ✅ Step 3: Check if AwardID already exists
    elseif (isset($_POST['check-awardID'])) {
        $awardID = intval($_POST['awardID']);

        $stmt = $db->prepare("SELECT COUNT(*) AS count FROM award_id WHERE awardID = ?");
        $stmt->bind_param("i", $awardID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        jsonResponse(['exists' => $result['count'] > 0]);
    }

    // ✅ Step 4: Save BaseID, AwardID, Title, and Synopsis (only if unique)
    elseif (isset($_POST['save-baseID'])) {
        $newBaseID = intval($_POST['baseID']);
        $awardID = intval($_POST['awardID']);
        $title = $_POST['title'] ?? null;
        $synopsis = $_POST['synopsis'] ?? null;

        if (!$newBaseID || !$awardID || !$title || !$synopsis) {
            jsonResponse(['error' => 'Missing parameters'], 400);
        }

        // 🔥 Check for duplicate before inserting
        $checkStmt = $db->prepare("SELECT awardID FROM award_id WHERE title = ? AND synopsis = ?");
        $checkStmt->bind_param("ss", $title, $synopsis);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Existing combination found, return existing ID
            $row = $checkResult->fetch_assoc();
            jsonResponse(['exists' => true, 'awardID' => $row['awardID']]);
        } else {
            // Insert new record if unique
            $stmt = $db->prepare("
                INSERT INTO award_id (baseID, awardID, title, synopsis) 
                VALUES (?, ?, ?, ?)
            ");

            if (!$stmt) {
                jsonResponse(['error' => "Prepare failed: " . $db->error], 500);
            }

            $stmt->bind_param("iiss", $newBaseID, $awardID, $title, $synopsis);

            if ($stmt->execute()) {
                jsonResponse(['success' => 'BaseID and AwardID inserted successfully', 'awardID' => $awardID]);
            } else {
                jsonResponse(['error' => 'Failed to insert BaseID and AwardID'], 500);
            }

            $stmt->close();
        }
    } else {
        jsonResponse(['error' => 'Invalid request'], 400);
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
