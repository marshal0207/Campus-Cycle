<?php
header('Content-Type: application/json');

$usersFile = __DIR__ . '/users.txt'; // ensure file is in same folder

// Check if user already exists
function userExists($email, $usersFile) {
    if (!file_exists($usersFile)) return false;
    $file = fopen($usersFile, 'r');
    while (($line = fgets($file)) !== false) {
        $parts = explode('|', trim($line));
        if (count($parts) >= 2 && $email === $parts[1]) {
            fclose($file);
            return true;
        }
    }
    fclose($file);
    return false;
}

// Add a new user
function addUser($fullname, $email, $password, $usersFile) {
    $file = fopen($usersFile, 'a');
    fwrite($file, $fullname . '|' . $email . '|' . $password . PHP_EOL);
    fclose($file);
}

// Verify login
function checkLogin($email, $password, $usersFile) {
    if (!file_exists($usersFile)) return false;
    $file = fopen($usersFile, 'r');
    while (($line = fgets($file)) !== false) {
        $parts = explode('|', trim($line));
        if (count($parts) >= 3 && $email === $parts[1] && $password === $parts[2]) {
            fclose($file);
            return true;
        }
    }
    fclose($file);
    return false;
}

// Handle request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($action === 'signup') {
        if (!$fullname || !$email || !$password) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        if (userExists($email, $usersFile)) {
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        } else {
            addUser($fullname, $email, $password, $usersFile);
            echo json_encode(['success' => true, 'message' => 'Signup successful!']);
        }
        exit;
    }

    if ($action === 'login') {
        if (!$email || !$password) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        if (checkLogin($email, $password, $usersFile)) {
            echo json_encode(['success' => true, 'message' => 'Login successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    exit;
}
?>
