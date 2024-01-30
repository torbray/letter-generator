<?php

class Account {

    public $account_id = "";
    public $account_name = "";
    public $account_type = "";
    public $balance = "";
    public $open_date = "";

    function __construct($account_id, $account_type, $balance, $open_date) {
        $this -> account_id = $account_id;
        $this -> account_type = $account_type;
        $this -> balance = $balance;
        $this -> open_date = $open_date;
    }

    public function getNumber() {
        // Add leading zeros (assuming 7 leading zeros + 3 zeros at end)
        $acc = str_pad($this -> account_id, 7, '0', STR_PAD_LEFT);

        /**
         * For the purposes of the prototype, all accounts end in -000.
         * In the banking industry, accounts may be 030, be 2 digits as 30 etc. To simplify
         * for the purposes of the prototype, only one suffix will be ran
         */
        $acc .= '-000';

        return $acc;
    }

    public function getType() {
        return $this -> account_type;
    }

    public function getBalance() {
        return "$ " . number_format($this -> balance, 2);
    }

    public function getOpenDate() {
        return date("d/m/Y", strtotime($this -> open_date));
    }

}

?>