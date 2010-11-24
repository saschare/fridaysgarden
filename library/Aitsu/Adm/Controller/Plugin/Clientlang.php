<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Navigation.php 18379 2010-08-27 11:05:16Z akm $}
 */

class Aitsu_Adm_Controller_Plugin_Clientlang extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		$clients = Aitsu_Persistence_Clients :: getAll();

		foreach ($clients as $key => $client) {
			if (Aitsu_Adm_User :: getInstance() == null || !Aitsu_Adm_User :: getInstance()->isAllowed(array (
					'client' => $client->idclient
				))) {
				unset ($clients[$key]);
			}
		}

		Zend_Registry :: set('clients', $clients);

		if (!isset (Aitsu_Registry :: get()->session->currentClient)) {
			Aitsu_Registry :: get()->session->currentClient = $clients[0]->idclient;
		}

		if ($request->getParam('setCurrentClient') != null) {
			Aitsu_Registry :: get()->session->currentClient = $request->getParam('setCurrentClient');
		}

		$langs = Aitsu_Persistence_Language :: getByClient(Aitsu_Registry :: get()->session->currentClient);
		
		$validLangs = array();
		foreach ($langs as $key => $lang) {
			if (Aitsu_Adm_User :: getInstance() == null || !Aitsu_Adm_User :: getInstance()->isAllowed(array (
					'language' => $lang->idlang
				))) {
				unset ($langs[$key]);
			} else {
				$validLangs[] = $lang->idlang;
			}
		}
		
		Zend_Registry :: set('langs', $langs);

		if (!isset (Aitsu_Registry :: get()->session->currentLanguage)) {
			/*
			 * First access, no session established yet. Language is
			 * set to first language of the current client the user has
			 * access to.
			 */
			Aitsu_Registry :: get()->session->currentLanguage = $langs[0]->idlang;
		}

		if ($request->getParam('setCurrentLanguage') != null) {
			/*
			 * Language has been chosen in the backend.
			 */
			Aitsu_Registry :: get()->session->currentLanguage = $request->getParam('setCurrentLanguage');
		}
		
		if (!in_array(Aitsu_Registry :: get()->session->currentLanguage, $validLangs)) {
			/*
			 * The user is either not privileged to use the specified language or the 
			 * language does not correspond to the current client. In either case the
			 * language has to be reset to the first language the user has access to.
			 */
			Aitsu_Registry :: get()->session->currentLanguage = $langs[0]->idlang;
		}
		
trigger_error(var_export($langs, true));
trigger_error('lang: ' . Aitsu_Registry :: get()->session->currentLanguage);
		Aitsu_Registry :: get()->env->idlang = Aitsu_Registry :: get()->session->currentLanguage;
	}
}