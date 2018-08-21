<?php

class Table extends AppModel {

    public $validate = array(
        'name' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi"
        ]
    );
    public $belongsTo = array(
    );
    public $hasOne = array(
        
    );
}
