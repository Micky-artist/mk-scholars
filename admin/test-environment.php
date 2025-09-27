<?php
include("./dbconnections/connection.php");

echo "<h2>Environment Detection Test</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Property</th><th>Value</th></tr>";
echo "<tr><td>isOnline()</td><td>" . (isOnline() ? 'Online (Production)' : 'Offline (Local)') . "</td></tr>";
echo "<tr><td>getBaseUrl()</td><td>" . getBaseUrl() . "</td></tr>";
echo "<tr><td>getAssetUrl('assets/css/style.css')</td><td>" . getAssetUrl('assets/css/style.css') . "</td></tr>";
echo "<tr><td>getImageUrl('uploads/courses/images/test.jpg')</td><td>" . getImageUrl('uploads/courses/images/test.jpg') . "</td></tr>";
echo "<tr><td>HTTP_HOST</td><td>" . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>SERVER_NAME</td><td>" . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>SERVER_ADDR</td><td>" . ($_SERVER['SERVER_ADDR'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>DOCUMENT_ROOT</td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</td></tr>";
echo "</table>";

echo "<h3>Test Image URLs</h3>";
$testPaths = [
    'uploads/courses/images/course1.jpg',
    './uploads/courses/images/course2.jpg',
    '../uploads/courses/images/course3.jpg',
    'assets/images/placeholder.jpg'
];

foreach ($testPaths as $path) {
    echo "<p><strong>Path:</strong> $path<br>";
    echo "<strong>Result:</strong> " . getImageUrl($path) . "</p>";
}
?>
