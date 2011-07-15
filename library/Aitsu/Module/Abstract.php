<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Module_Abstract {

	protected $_allowEdit = true;

	protected $_id;
	protected $_type = null;
	protected $_view = null;
	protected $_context = null;
	protected $_index = null;
	protected $_params = null;
	
	/*
	 * _isVolatile flags the cached output to be volatile in the
	 * sense that each and every single publish event should result
	 * in a loss of the cached data. _isVolatile should be set to
	 * true, if the output is dependend on data outside the scope of
	 * the current article (page).
	 */
	protected $_isVolatile = false;

	public static function init($context) {

		$output = '';

		$instance = new $context['className'] ();

		/*
		 * Suppress edit option, if _allowEdit is set to false.
		 */
		if (!$instance->_allowEdit) {
			$className = str_replace('_', '.', $context['className']);
			$className = preg_replace('/^(?:Skin\\.Module|Module)\\./', "", $className);
			$className = preg_replace('/\\.Class$/', "", $className);
			Aitsu_Content_Edit :: noEdit($className, true);
		}

		$instance->_context = $context;

		$instance->_context['index'] = preg_replace('/[^a-zA-Z_0-9]/', '_', $instance->_context['index']);
		$instance->_context['index'] = str_replace('.', '_', $instance->_context['index']);

		$instance->_index = empty ($instance->_context['index']) ? 'noindex' : $instance->_context['index'];

		if (!empty ($instance->_context['params'])) {
			$instance->_params = Aitsu_Util :: parseSimpleIni($instance->_context['params']);
		}
		
		/*
		 * Execution of the _init method is done, even a 
		 * valid cache is available.
		 */
		$output = $instance->_init();

		if ($instance->_cachingPeriod() > 0) {
			if ($instance->_get($context['className'], $output)) {
				return $output;
			}
		}

		/*
		 * Execution of the _main method is only done, if caching
		 * is disabled or there is no valid cache.
		 */
		$output .= $instance->_main();

		if ($instance->_cachingPeriod() > 0) {
			$instance->_save($output, $instance->_cachingPeriod());
		}

		return $output;
	}

	protected function _init() {

		return '';
	}

	protected function _main() {

		return '';
	}

	protected function _cachingPeriod() {

		return 0;
	}

	/**
	 * Overwrite this method accordingly if the module has
	 * dependencies to ensure it returns true only, if all
	 * the dependencies evaluate to true.
	 * 
	 * @return Boolean Returns true, if the module is ready to be used. False otherwise.
	 */
	public static function isReady() {

		return true;
	}

	/**
	 * This method is called, if the module returns false on isReady
	 * call and the user asks for installation. After a call of install
	 * the method isReady should return true from then on.
	 */
	public static function install() {

		return true;
	}

	protected function _getView() {

		if ($this->_view != null) {
			return $this->_view;
		}

		$class = get_class($this);

		$search = array (
			'_Class',
			'_'
		);
		$replace = array (
			'',
			'/'
		);

		$modulePath = str_replace($search, $replace, $class);

		$view = new Zend_View();

		$search = array (
			'Aitsu/Ee/Module/',
			'Local/Module/',
			'Skin/Module/',
			'Module/'
		);
		$replace = array (
			'',
			'',
			'',
			''
		);
		$skinModulePath = APPLICATION_PATH . "/skins/" . (isset (Aitsu_Registry :: get()->config->skin) ? Aitsu_Registry :: get()->config->skin : 'x') . "/module/" . str_replace($search, $replace, $modulePath);

		if (file_exists($skinModulePath)) {
			$view->setScriptPath($skinModulePath);
		}
		elseif (file_exists(APPLICATION_PATH . '/modules/' . str_replace('_', '/', substr($class, 7, strlen($class) - 13)))) {
			$view->setScriptPath(APPLICATION_PATH . '/modules/' . str_replace('_', '/', substr($class, 7, strlen($class) - 13)));
		} else {
			$path = realpath(APPLICATION_PATH . '/../library');
			$view->setScriptPath($path . '/' . $modulePath);
		}

		return $view;
	}

	protected function _get($id, & $output, $overwriteDisable = false) {

		$id = $this->_normalizeIndex($id);

		$this->_id = $id . '_' . Aitsu_Registry :: get()->env->idartlang . '_' . $this->_index;
		$cache = Aitsu_Cache :: getInstance($this->_id, $overwriteDisable);

		if (Aitsu_Registry :: isEdit()) {
			$cache->remove();
			return false;
		}

		if ($cache->isValid()) {
			$output = $cache->load();
			return true;
		}

		return false;
	}

	protected function _remove($id = null) {

		$id = $this->_normalizeIndex($id);

		if ($id != null) {
			$this->_id = $id . '_' . Aitsu_Registry :: get()->env->idartlang;
		}

		Aitsu_Cache :: getInstance($this->_id)->remove();
	}

	protected function _save($data, $lifeTime = null) {

		if ($data == null) {
			$data = '';
		}

		if (!is_string($data)) {
			throw new Exception('non-string data to be cached in ' . get_class($this));
		}

		$cache = Aitsu_Cache :: getInstance($this->_id);

		if (Aitsu_Registry :: isEdit()) {
			return false;
		}

		if (!empty ($this->_type)) {
			$tags[] = 'type_' . $this->_type;
		}
		$tags[] = 'cat_' . Aitsu_Registry :: get()->env->idcat;
		$tags[] = 'art_' . Aitsu_Registry :: get()->env->idart;
		
		if ($this->_isVolatile) {
			/*
			 * The volatile tag is set to make sure the cached output
			 * is deleted on every publish event. Refer to the comment
			 * on the class member _isVolatile for details.
			 */
			$tags[] = 'volatile';
		}

		if (!empty ($lifeTime)) {
			if ($lifeTime == 'eternal') {
				$lifeTime = 60 * 60 * 24 * 365; // one year
			}
			$cache->setLifetime($lifeTime);
		}

		$cache->save($data, $tags);
	}

	protected function _getTemplates() {

		$return = array ();

		$templates = $this->_getView()->getScriptPaths();

		$templateFiles = array ();
		foreach ($templates as $template) {
			$files = glob($template . '*.phtml');
			if (!empty ($files)) {
				$templateFiles = array_merge($templateFiles, $files);
			}
		}

		foreach ($templateFiles as $file) {
			$content = file_get_contents($file);
			if (preg_match('/^<\\!\\-{2}\\s*(.*?)\\s*\\-{2}>/', $content, $match)) {
				$return[$match[1]] = substr(basename($file), 0, -6);
			}
		}

		return $return;
	}

	private function _normalizeIndex($id) {

		return preg_replace('/[^a-zA-Z_0-9]/', '', $id);
	}

}
?>