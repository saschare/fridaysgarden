<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Module_Navigation_Sub_Class extends Aitsu_Module_Tree_Abstract {

    protected $type = 'navigation';
    protected $_allowEdit = false;

    protected function _main() {

        $template = isset($this->_params->template) ? $this->_params->template : 'index';
        $firstLevel = isset($this->_params->firstLevel) ? $this->_params->firstLevel : '0';
        
        $bc = Aitsu_Persistence_View_Category :: breadCrumb();
        
        $view = $this->_getView();
        $view->nav = Aitsu_Persistence_View_Category :: nav2($bc[$firstLevel]['idcat']);

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return Aitsu_Util_Date::secondsUntilEndOf('day');
    }

}