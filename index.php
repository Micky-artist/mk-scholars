<?php
// driving-school/index.php
// Permanent redirect to the driving school page
header('HTTP/1.1 301 Moved Permanently');
header('Location: /driving-school/');
exit;