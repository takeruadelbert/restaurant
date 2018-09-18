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
}
