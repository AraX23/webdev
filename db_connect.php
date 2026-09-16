<?php
/**
 * ComMEETtee - Database Connection Bridge
 * 
 * Provides global $conn, $db, and $pdo variables for scripts.
 * 
 * Usage in any PHP file:
 *   require_once __DIR__ . '/db_connect.php';
 *   // Now use $conn or getDatabase()
 */

require_once __DIR__ . '/database.php';

try {
    $conn = getDatabase();
    $db = $conn;
    $pdo = $conn;
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}
