<?php

class MenuCategory extends AppModel {

    public $validate = array(
        'name' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi"
        ],
        'uniq' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi"
        ]
    );
    public $belongsTo = array(
        "Parent" => [
            "className" => "MenuCategory",
            "foreignKey" => "parent_id"
        ]
    );
    public $hasOne = array(
        
    );

    function list_category($conds = []) {
        $list = $this->find("list",[
            "fields" => [
                "MenuCategory.id",
                "MenuCategory.name",
                "Parent.name"
            ],
            "contain" => [
                "Parent"
            ],
            "order" => "MenuCategory.name"
        ]);
        if(!empty($list[0])) {
            $list['Kategori Utama'] = $list[0];
            unset($list[0]);
        }
        return $list;
    }
}
