<?php

App::uses('AppController', 'Controller');

class MenuCategoriesController extends AppController {

    var $name = "MenuCategories";
    var $disabledAction=array(
    );
    var $contain = array(
        "Parent"
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
        $this->set("parents", ClassRegistry::init("MenuCategory")->list_category());
    }
    
    function api_get_category_list() {
        $this->autoRender = false;
        if($this->request->is("GET")) {
            $data = $this->MenuCategory->find("list",[
                "fields" => [
                    "MenuCategory.id",
                    "MenuCategory.name"
                ],
                "recursive" => -1,
                "order" => "MenuCategory.name"
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
