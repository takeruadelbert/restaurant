<?php

class OrderDetail extends AppModel {

    public $validate = array(
        'order_id' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi."
        ],
//        "resto_menu_id" => [
//            "rule" => "notEmpty",
//            "message" => "Wajib Diisi"
//        ]
    );
    public $belongsTo = array(
        "RestoMenu"
    );
    public $hasOne = array(
        
    );
}