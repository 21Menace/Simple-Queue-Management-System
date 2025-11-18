<?php
require_once __DIR__ . '/../../src/session.php';
require_once __DIR__ . '/../../config/config.php';
require_login();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Cache-Control');

// Function to send SSE data
function sendSSE($data) {
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Send initial connection message
sendSSE(['type' => 'connected', 'message' => 'Connected to notification stream']);

$pdo = get_pdo();
$userId = current_user_id();
$notificationFile = __DIR__ . '/../../notifications/' . $userId . '.json';

// Keep track of the last notification time to avoid duplicates
$lastNotificationTime = null;

while (true) {
    try {
        // First check for specific notifications from admin actions
        if (file_exists($notificationFile)) {
            $notification = json_decode(file_get_contents($notificationFile), true);
            
            if ($notification && (time() - $notification['timestamp']) < 300) {
                // Delete the notification file after reading
                unlink($notificationFile);
                sendSSE($notification);
            }
        }
        
        // Check if user is in any queue
        $stmt = $pdo->prepare('
            SELECT qe.id, qe.queue_id, qe.status, q.name as queue_name, qe.joined_at
            FROM queue_entries qe
            JOIN queues q ON q.id = qe.queue_id
            WHERE qe.user_id = ? AND qe.status = "waiting"
            ORDER BY qe.id ASC LIMIT 1
        ');
        $stmt->execute([$userId]);
        $userEntry = $stmt->fetch();
        
        if ($userEntry) {
            // Check if user is next in line
            $nextStmt = $pdo->prepare('
                SELECT COUNT(*) as position FROM queue_entries 
                WHERE queue_id = ? AND status = "waiting" AND id < ?
            ');
            $nextStmt->execute([$userEntry['queue_id'], $userEntry['id']]);
            $position = $nextStmt->fetch()['position'] + 1;
            
            // Send position update
            sendSSE([
                'type' => 'position_update',
                'message' => "You are #{$position} in line for " . $userEntry['queue_name'],
                'queue_name' => $userEntry['queue_name'],
                'position' => $position
            ]);
        } else {
            // User not in any queue
            sendSSE([
                'type' => 'not_in_queue',
                'message' => 'You are not currently in any queue'
            ]);
        }
        
    } catch (Exception $e) {
        sendSSE([
            'type' => 'error',
            'message' => 'Error checking queue status: ' . $e->getMessage()
        ]);
    }
    
    // Wait 3 seconds before next check
    sleep(3);
    
    // Check if connection is still alive
    if (connection_aborted()) {
        break;
    }
}
?>
