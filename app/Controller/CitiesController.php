<?php

App::uses('AppController', 'Controller');

class CitiesController extends AppController {

    var $name = "Cities";
    var $disabledAction = array(
        "admin_index",
        "admin_add",
        "admin_edit",
        "admin_multiple_delete",
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->_setPageInfo("admin_index", "");
        $this->_setPageInfo("admin_add", "");
        $this->_setPageInfo("admin_edit", "");
    }

    function admin_list_by_state($state_id = null) {
        $this->autoRender = false;
        $data = $this->City->find("list", [
            "fields" => [
                "City.id",
                "City.label",
            ],
            "conditions" => [
                "City.state_id" => $state_id
            ]
        ]);
        echo json_encode($this->_generateStatusCode(205, null, $data));
    }

    function admin_list($state_id = false) {
        if ($state_id === false) {
            $data = $this->City->find("list", [
                "fields" => [
                    "City.id",
                    "City.name",
                ]
            ]);
        } else {
            $data = $this->City->find("all", [
                "fields" => [
                    "City.id",
                    "City.name",
                ],
                "conditions" => [
                    "City.state_id" => $state_id
                ],
                "order" => "City.name"
            ]);
            $result = [];
            foreach ($data as $index => $cities) {
                $result[] = [
                    $index => [
                        $cities['City']['id'] => $cities['City']['name']
                    ]
                ];
            }
        }
        echo json_encode($this->_generateStatusCode(205, null, $result));
        die;
    }
}
