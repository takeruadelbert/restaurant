<?php

App::uses('AppController', 'Controller');

class TablesController extends AppController {

    var $name = "Tables";
    var $disabledAction=array(
    );
    var $contain = array(
    );
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->_setPageInfo("admin_index", "");
        $this->_setPageInfo("admin_add", "");
        $this->_setPageInfo("admin_edit", "");
    }
    
    function api_get_table_list() {
        $this->autoRender = false;
        if($this->request->is("GET")) {
            $data = $this->Table->find("list",[
                "fields" => [
                    "Table.id",
                    "Table.name"
                ],
                "recursive" => -1,
                'order' => "Table.name"
            ]);
            if(!empty($data)) {
                return json_encode($this->_generateStatusCode(205, "Data Found.", $data));
            } else {
                return json_encode($this->_generateStatusCode(401));
            }
        } else {
            return json_encode($this->_generateStatusCode(400));
        }
    }
}
