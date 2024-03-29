<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */
class Aitsu_Plugin_Meta_Article_Controller extends Moraso_Adm_Plugin_Controller {

    const ID = '4ca98c9e-85ec-4228-8171-12657f000101';

    public function init() {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart) {

        return (object) array(
                    'name' => 'meta',
                    'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Meta'),
                    'enabled' => self :: getPosition($idart, 'meta'),
                    'position' => self :: getPosition($idart, 'meta'),
                    'id' => self :: ID
        );
    }

    public function indexAction() {

        $id = $this->getRequest()->getParam('idart');

        $classExplode = explode('_', __CLASS__);

        $form = Aitsu_Forms::factory(strtolower($classExplode[2]), APPLICATION_LIBPATH . '/' . $classExplode[0] . '/' . $classExplode[1] . '/' . $classExplode[2] . '/' . $classExplode[3] . '/forms/meta.ini');
        $form->title = Aitsu_Translate :: translate('Meta data');
        $form->url = $this->view->url(array('namespace' => 'aitsu', 'plugin' => 'meta', 'area' => 'article', 'paction' => 'index'), 'plugin');

        $data = Aitsu_Persistence_ArticleMeta :: factory($id)->load();

        $data->robots = explode(', ', $data->robots);
        $data->idart = $id;
        $form->setValues($data->toArray());

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {
                Aitsu_Event :: raise('backend.article.edit.save.start', array(
                    'idart' => $id
                ));

                /*
                 * Persist the data.
                 */
                $data->setValues($form->getValues())->save();

                $this->_helper->json((object) array(
                            'success' => true,
                            'data' => (object) $data->toArray()
                ));
            } else {
                $this->_helper->json((object) array(
                            'success' => false,
                            'errors' => $form->getErrors()
                ));
            }
        } catch (Exception $e) {
            $this->_helper->json((object) array(
                        'success' => false,
                        'exception' => true,
                        'message' => $e->getMessage()
            ));
        }
    }

}