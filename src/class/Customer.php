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
        return $this -> first_name . " " . $this -> last_name;
    }

    public function getFormalName() {
        return $this -> title . " " . $this -> last_name;
    }

    public function getAddress() {
        return $this -> address;
    }

    /**
     * For prototype purposes only
     * 
     * See Account class for account name reasoning
     */
    public function generateAccountName() {
        $format = $this -> last_name . " " . $this -> first_name;

        return strtoupper($format);
    }

}

?>