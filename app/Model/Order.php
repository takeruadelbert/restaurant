<?php

class Order extends AppModel {

    public $validate = array(
        "no_order" => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi."
        ],
        'table_id' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi."
        ],
        "account_id" => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi."
        ]
    );
    public $belongsTo = array(
        "Table",
        "Account",
        "OrderStatus"
    );
    public $hasOne = array(
        
    );
    public $hasMany = array(
        "OrderDetail"
    );
    
    function generate_order_number() {
        $inc_id = 1;
        $m = date('m');
        $mRoman = romanic_number($m);
        $Y = date('Y');
        $testCode = "ORD/$mRoman/$Y/[0-9]{5}";
        $lastRecord = $this->find('first', array('conditions' => array('and' => array("Order.no_order regexp" => $testCode)), 'order' => "CAST(Order.no_order as SIGNED) DESC"));
        if (!empty($lastRecord)) {
            $current = explode("/", $lastRecord['Order']['no_order']);
            $inc_id += $current[count($current) - 1];
        }
        $inc_id = sprintf("%05d", $inc_id);
        $kode = "ORD/$mRoman/$Y/$inc_id";
        return $kode;
    }
}