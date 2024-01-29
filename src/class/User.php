<?php

class User {

    public $first_name = "";
    public $last_name = "";
    public $username = "";
    public $job_title = "";

    function __construct($first, $last, $username, $job_title) {
        $this -> first_name = $first;
        $this -> last_name = $last;
        $this -> username = $username;
        $this -> job_title = $job_title;
    }
}

?>