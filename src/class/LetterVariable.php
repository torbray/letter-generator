<?php

class LetterVariable {

    public $category = "";
    public $key = "";
    public $method = "";
    public $box = "";

    function __construct($category, $key, $method, $box) {
        $this -> category = $category;
        $this -> key = $key;
        $this -> method = $method;
        $this -> box = $box;
    }
}

?>