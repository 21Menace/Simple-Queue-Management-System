<?php
require_once __DIR__ . '/../../src/session.php';
require_once __DIR__ . '/../../config/config.php';
require_login();

header('Content-Type: application/json');

$userId = current_user_id();
$notificationFile = __DIR__ . '/../../notifications/' . $userId . '.json';

if (file_exists($notificationFile)) {
    $notification = json_decode(file_get_contents($notificationFile), true);
    
    // Check if notification is recent (within last 5 minutes)
    if ($notification && (time() - $notification['timestamp']) < 300) {
        // Delete the notification file after reading
        unlink($notificationFile);
        
        echo json_encode([
            'has_notification' => true,
            'notification' => $notification
        ]);
    } else {
        // Delete old notification
        unlink($notificationFile);
        echo json_encode(['has_notification' => false]);
    }
} else {
    echo json_encode(['has_notification' => false]);
}
?>
