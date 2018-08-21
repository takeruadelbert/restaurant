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
}
