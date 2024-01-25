<?php

class Customer {

    public $first_name = "";
    public $last_name = "";
    public $address = "";
    public $title = "";

    function __construct($first, $last, $address, $title) {
        $this -> first_name = $first;
        $this -> last_name = $last;
        $this -> address = $address;
        $this -> title = $title;
    }

    public function getFullName() {
        return self::$first_name . " " . self::$last_name;
    }

}

?>