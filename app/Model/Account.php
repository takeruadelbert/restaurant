<?php

class Account extends AppModel {
    public $validate = array(
        
    );
    
    public $belongsTo = array(
        'AccountStatus',
        'Employee',
        "PasswordReset",
    );
    
    public $hasOne = array(
        "User" => array(
            "dependent" => true
        ),
        "Biodata" => array(
            "dependent" => true
        ),
    );

    public function get_list_full_name() {
        $result = [];
        $dataAccount = $this->find("all",[
            "conditions" => [
                "Account.account_status_id" => 1,
            ],
            "contain" => [
                "Biodata"
            ],
            "Order" => "Biodata.full_name"
        ]);
        if(!empty($dataAccount)) {
            foreach ($dataAccount as $account) {
                $result[$account['Account']['id']] = $account['Biodata']['full_name'];
            }
        }
        return $result;
    }
    
    public function get_name($id) {
        if(!empty($id)) {
            $dataAccount = $this->find("first",[
                "conditions" => [
                    "Account.id" => $id
                ],
                "contain" > [
                    "Biodata"
                ]
            ]);
            if(!empty($dataAccount)) {
                return $dataAccount['Biodata']['full_name'];
            } else {
                return "";
            }
        } else {
            return "";
        }
    }
}
