<?php
    $res = `screen -S pi/Guest-Door-Py -p 0 -X stuff "I_READ_92CA591C^M"`;
    echo $res;
?>
