<?php
// Redirect root to /home
header('HTTP/1.1 307 Temporary Redirect');
header('Location: /home');
exit;
