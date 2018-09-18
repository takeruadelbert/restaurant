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
        "full_label" => "concat(Device.name, ' (', Device.ip_address, ')')"
    );

    function beforeValidate($options = array()) {
        
    }

    function deleteData($id = null) {
        return $this->delete($id);
    }

}

?>
