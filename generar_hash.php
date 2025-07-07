<?php

$mi_password_secreta = 'mediterrane0123'; 

$hash = password_hash($mi_password_secreta, PASSWORD_BCRYPT);

echo "Copia este hash y pégalo en tu archivo login.php:<br><br>";
echo "<strong>" . $hash . "</strong>";
?>