<?php

App::uses('AppController', 'Controller');

class OrdersController extends AppController {

    var $name = "Orders";
    var $disabledAction=array(
    );
    var $contain = array(
        "Table",
        "OrderDetail" => [
            "RestoMenu"
        ]
    );
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->_setPageInfo("admin_index", "");
        $this->_setPageInfo("admin_add", "");
        $this->_setPageInfo("admin_edit", "");
    }
    
    function beforeRender() {
        $this->options();
        parent::beforeRender();
    }
    
    function options() {
        $this->set("tables", ClassRegistry::init("Table")->find("list", ['fields' => ['Table.id', 'Table.name']]));
    }
    
    function api_post_order() {
        $this->autoRender = false;
        if($this->request->is("POST")) {
//            $resto_menu_id = $this->data['resto_menu_id'];
//            $quantity = $this->data['quantity'];
//            $amount = $this->data['amount']; // price for each menu
//            $note = $this->data['note'];
//            $ppn = $this->data['ppn'];
//            $discount = $this->data['discount'];
//            if(!empty($resto_menu_id) && !empty($quantity) && !empty($amount) && !empty($note) && !empty($ppn) && !empty($discount)) {
//                $total = $amount * $quantity;
//            } else {
//                $err_item = [];
//                if(empty($resto_menu_id)) {
//                    $err_item[] = "resto_menu_id";
//                } else if(empty($quantity)) {
//                    $err_item[] = "quantity";
//                } else if(empty($amount)) {
//                    $err_item[] = "amount";
//                } else if(empty($note)) {
//                    $err_item[] = "note";
//                } else if(empty($ppn)) {
//                    $err_item[] = "ppn";
//                } else if(empty($discount)) {
//                    $err_item[] = "discount";
//                }
//                $message = "There's error(s) with following field(s) : ";
//                for($i = 0; $i < count($err_item); $i++) {
//                    if($i != count($err_item) - 1) {
//                        $message = "'{$err_item[$i]}', ";
//                    } else {
//                        $message = "'{$err_item[$i]}'.";
//                    }
//                }
//                return json_encode($this->_generateStatusCode(405, $message));
//            }
        } else {
            return json_encode($this->_generateStatusCode(400, "Invalid Request Type."));
        }
    }
}
