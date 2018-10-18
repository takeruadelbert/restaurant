<?php

class Table extends AppModel {

    public $validate = array(
        'name' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi"
        ],
        'pos_x' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi."
        ],
        'pos_y' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi."
        ],
        "label" => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi."
        ]
    );
    public $belongsTo = array(
    );
    public $hasOne = array(
        
    );
    
    function check_position($x = null, $y = null) {
        if(!empty($x) && !empty($y)) {
            $tablePosition = $this->find("first",[
                "conditions" => [
                    "Table.pos_x" => $x,
                    "Table.pos_y" => $y
                ],
                "recursive" => -1
            ]);
            if(!empty($tablePosition)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
