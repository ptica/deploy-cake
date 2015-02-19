<?php
Class GruntfileShell extends Shell {

	public function main() {
		if (empty($this->args)) {
			$this->help();
			return;
		}

		$this->create();
	}

	/**
	 * help
	 *
	 */
	public function help() {
		$this->hr();
		$this->out('Usage: cake gruntfile create <sitename> ');
		$this->hr();
		$this->out('Parameters:');
		$this->out('	<sitename>');
		$this->out('		(eg. example.com)');
		$this->out();
		$this->out('	<template>');
		$this->out('		template file. (default: "default")');
		$this->out();
		$this->hr();
	}

	public function create() {
		// vars used in the template
		$domain  = $this->args[0];
		$webroot = WWW_ROOT;
		$logpath = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'logs' . DS;

		$template = 'default';
		if (!empty($this->params['t']) && is_file($this->_getTemplatePath($this->params['t']) . $this->params['t'] . '.ctp')) {
			$template = $this->params['t'];
		}

		ob_start();
		include $this->_getTemplatePath($template) . $template . '.ctp';
		$conf = ob_get_contents();
		ob_end_clean();

		$this->out($conf);

		$this->createFile(ROOT . DS . APP_DIR . DS . 'Gruntfile.js', $conf);
	}

	protected function _getTemplatePath($template='') {
		$app_config = ROOT . DS . APP_DIR . DS . 'Config' . DS . 'Gruntfile' . DS;
		if (is_file($app_config . $template . '.ctp')) {
			// app Config/Vhost/{template}.ctp
			return $app_config;
		} else {
			//  Config/Vhost/{template}.ctp
			return dirname(dirname(dirname(__FILE__))) . DS . 'Config' . DS . 'Gruntfile' . DS;
		}
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->addOptions('template', array(
			'short' => 't',
			'help' => 'template: default | TBD',
			'default' => 'default',
		));
		return $parser;
	}
}
