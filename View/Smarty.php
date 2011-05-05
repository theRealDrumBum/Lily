<?php
/**
 * Copyright (c) 2010, 2011 All rights reserved, Matt Ward
 * This code is subject to the copyright agreement found in
 * the project root's LICENSE file.
 */
/**
 * LilypadMVC_View_Smarty class.
 * @author Matt Ward
 * @extends LilypadMVC_View_Abstract
 */
class LilypadMVC_View_Smarty extends LilypadMVC_View_Abstract
{
	protected $smarty;


	public function __construct($partials_dir)
	{
		parent::__construct($partials_dir);
		$this->file_extension	= 'tpl';

		if (!defined('SMARTY_LIB')) {
			throw new Exception("The 'SMARTY_LIB' constant is not defined and required to use a smarty view");
		}
		require_once(constant('SMARTY_LIB') . '/libs/Smarty.class.php');

		$this->smarty	= new Smarty();
		$this->smarty->language = 'en';
		$this->smarty->assign('PARTIAL_DIR', $partials_dir);

	}

	public function render($template) {
		if (substr($template, '.' . $this->file_extension) == 0) {
			$template .= '.' . $this->file_extension;
		}

		$log = LilypadMVC_Application::getLogger();
		$log->debug($template);
		ob_start();
		$this->smarty->display($template);
		return ob_get_clean();
	}

	public function __set($key, $value) {
		$this->smarty->assign_by_ref($key, $value);
	}

	public function __get($key) {
		return null;
	}

}