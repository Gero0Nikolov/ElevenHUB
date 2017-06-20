<?php
$ch = curl_init("http://11hub.net/wp-cron.php?check_tasks");
$fp = fopen("log.txt", "w");
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);
curl_close($ch);
fclose($fp);
?>
