<?php

class Device extends AppModel {

    var $name = 'Device';
    var $belongsTo = array(
    );
    var $hasOne = array(
    );
    var $hasMany = array(
    );
    var $validate = array(
        'name' => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi"
        ],
        "ip_address" => [
            "rule" => "notEmpty",
            "message" => "Wajib Diisi."
        ]
    );
    var $virtualFields = array(
    );

    function beforeValidate($options = array()) {
        
    }

    function deleteData($id = null) {
        return $this->delete($id);
    }

}

?>
