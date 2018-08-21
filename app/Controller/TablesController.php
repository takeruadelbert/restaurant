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
}
