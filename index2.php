<?php
$geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $_SERVER['REMOTE_ADDR']));
echo "<script>region='" . $geo["geoplugin_regionName"] . "'; country='" . $geo["geoplugin_countryName"] . "'</script>";
