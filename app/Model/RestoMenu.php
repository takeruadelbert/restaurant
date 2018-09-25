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
        ],
        "description" => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi"
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
    
    function get_list_menu() {
        $result = $this->find("list",[
            "fields" => [
                "RestoMenu.id",
                "RestoMenu.name",
                "MenuCategory.name"
            ],
            "contain" => [
                "MenuCategory"
            ],
            "order" => [
                "MenuCategory.name",
                "RestoMenu.name"                
            ]
        ]);
        return $result;
    }
}