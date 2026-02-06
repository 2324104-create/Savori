<?php
echo "<h2>🔐 Checking File Permissions</h2>";
echo "<style>table {border-collapse: collapse; width: 100%;} th, td {border: 1px solid #ddd; padding: 8px;} th {background: #4CAF50; color: white;}</style>";

$files = [
    'index.php',
    'config/config.php',
    'config/database.php',
    'includes/database_connect.php',
    'includes/header.php',
    'includes/footer.php',
    'pages/home.php',
    '.htaccess'
];

echo "<table>";
echo "<tr><th>File</th><th>Exists</th><th>Readable</th><th>Writable</th><th>Size</th></tr>";

foreach ($files as $file) {
    if (file_exists($file)) {
        $exists = "✅ Yes";
        $readable = is_readable($file) ? "✅ Yes" : "❌ No";
        $writable = is_writable($file) ? "✅ Yes" : "❌ No";
        $size = filesize($file) . " bytes";
    } else {
        $exists = "❌ No";
        $readable = "-";
        $writable = "-";
        $size = "-";
    }
    
    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td>$exists</td>";
    echo "<td>$readable</td>";
    echo "<td>$writable</td>";
    echo "<td>$size</td>";
    echo "</tr>";
}

echo "</table>";

// Check important directories
echo "<h3>Directory Permissions:</h3>";
$dirs = [
    'assets',
    'assets/css',
    'assets/js',
    'assets/images',
    'api',
    'models',
    'includes',
    'pages'
];

echo "<ul>";
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? "✅ Writable" : "❌ Not Writable";
        echo "<li>$dir: $writable</li>";
    } else {
        echo "<li>$dir: ❌ Does not exist</li>";
    }
}
echo "</ul>";

// Check Apache user
echo "<h3>PHP Info:</h3>";
echo "<p>PHP User: " . get_current_user() . "</p>";
echo "<p>PHP Process User: " . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'N/A') . "</p>";

// Try to create a test file
echo "<h3>Test Write Permission:</h3>";
$test_file = 'test_permission.txt';
if (file_put_contents($test_file, 'test')) {
    echo "<p style='color:green'>✅ Can write to root directory</p>";
    unlink($test_file);
} else {
    echo "<p style='color:red'>❌ Cannot write to root directory</p>";
}
?>