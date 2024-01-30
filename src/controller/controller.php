<?php

class Controller {

    public static function formatAccount($acc) {
        // Add leading zeros (assuming 7 leading zeros)
        $acc = str_pad($acc, 7, '0', STR_PAD_LEFT);
        
        // Add ending zeros (assuming 3 ending zeros)
        $acc = str_pad($acc, 10, '0', STR_PAD_RIGHT);

        return $acc;
    }

}

?>