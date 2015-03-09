<?php
Class GruntShell extends Shell {

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
		$this->out('Usage: cake grunt create <sitename> ');
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
		$sitename  = $this->args[0];
		$webroot = WWW_ROOT;
		$logpath = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'logs' . DS;
		$vars = compact('sitename', 'webroot', 'logpath');

		$template = 'default';
		if (!empty($this->params['t']) && is_file($this->_getTemplatePath($this->params['t']) . $this->params['t'] . '.ctp')) {
			$template = $this->params['t'];
		}

		$templates = array(
			'Gruntfile.js',
			'.bowerrc',
			'bower.json',
			'INSTALL.md',
			'package.json',
			'TODO.md'
		);
		foreach ($templates as $template) {
			$content = $this->renderTemplate($template, $vars);
			//$this->out($content);
			$this->createFile(ROOT . DS . APP_DIR . DS . $template, $content);
		}
	}

	protected function _getTemplatePath($template='') {
		$app_config = ROOT . DS . APP_DIR . DS . 'Config' . DS . 'Grunt' . DS;
		if (is_file($app_config . $template . '.ctp')) {
			// APP/Config/Vhost/{template}.ctp
			return $app_config;
		} else {
			// DeployPlugin/Config/Vhost/{template}.ctp
			return dirname(dirname(dirname(__FILE__))) . DS . 'Config' . DS . 'Grunt' . DS;
		}
	}

	private function renderTemplate($template='', $vars=array()) {
		extract($vars);
		ob_start();
		include $this->_getTemplatePath($template) . $template . '.ctp';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->addOption('template', array(
			'short' => 't',
			'help' => 'template: default | TBD',
			'default' => 'default',
		));
		return $parser;
	}
}
