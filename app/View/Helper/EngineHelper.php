<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class EngineHelper extends HtmlHelper {

    function toJSONoptions($list = array(), $empty = "") {
        $target = [];
        $target[] = [
            "value" => "",
            "label" => $empty,
        ];
        foreach ($list as $value => $label) {
            $target[] = [
                "value" => $value,
                "label" => $label,
            ];
        }
        return json_encode($target);
    }

    function toJSONoptionsWithParent($list = [], $encode = false) {
        $target = [];
        foreach ($list as $parent => $child) {
            $temp = [];
            foreach ($child as $value => $label) {
                $temp[] = [
                    "value" => $value,
                    "label" => $label
                ];
            }
            $target[] = [
                "parent" => $parent,
                "child" => $temp
            ];
        }
        return !$encode ? $target : json_encode($target);
    }
}
