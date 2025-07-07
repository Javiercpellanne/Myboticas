<?php
$mysqli = new mysqli("mysql-mybotica.alwaysdata.net", "mybotica", "Gerardito@1", "myboticaa_bdboticas");

if ($mysqli->connect_errno) {
    echo "Fallo conexión: " . $mysqli->connect_error;
} else {
    echo "✅ Conexión exitosa a AlwaysData!";
}
?>
