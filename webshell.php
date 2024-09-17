<?php
// Define the directory where uploaded files will be stored
$upload_dir = 'uploads/';

// Create the upload directory if it does not exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Function to handle file uploads
function upload_file($file) {
    global $upload_dir;
    
    // Define the path where the file will be saved
    $target_file = $upload_dir . basename($file['name']);
    
    // Check if the file was uploaded without errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            echo "File uploaded successfully: " . htmlspecialchars($file['name']);
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "Error uploading file: " . $file['error'];
    }
}

// Handle file upload requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    upload_file($_FILES['file']);
}

// Display the upload form
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload by Sheikh Nightshader</title>
</head>
<body>
    <h1>Upload File - Sheikh Nightshader</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <input type="submit" value="Upload">
    </form>
</body>
</html>
