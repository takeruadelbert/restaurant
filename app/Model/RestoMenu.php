<?php

class RestoMenu extends AppModel {

    public $validate = array(
        'name' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi"
        ],
        'menu_category_id' => [
            "rule" => 'notEmpty',
            'message' => 'Wajib Diisi'
        ],
        "price" => [
            "rule" => "validate_price",
            "message" => "Harga Harus > 0"
        ]
    );
    public $belongsTo = array(
        "MenuCategory"
    );
    public $hasOne = array(
        
    );
    
    function validate_price() {
        return $this->data['RestoMenu']['price'] <= 0 ? false : true;
    }
}