<?php
Class VhostShell extends Shell {

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
		$this->out('Usage: cake vhost create <vhostname> [-t <template>]');
		$this->hr();
		$this->out('Parameters:');
		$this->out('	<vhostname>');
		$this->out('		(eg. vhost.example.com)');
		$this->out();
		$this->out('	<template>');
		$this->out('		template file. (default: "apache")');
		$this->out();
		$this->hr();
	}

	public function create() {
		// vars used in the template
		$domain  = $this->args[0];
		$webroot = WWW_ROOT;
		$logpath = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'logs' . DS;
		
		// BEWARE win apache also needs unix DS
		$webroot = str_replace('\\', '/', $webroot);
		$logpath = str_replace('\\', '/', $logpath);

		$template = 'apache';
		if (!empty($this->params['t']) && is_file($this->_getTemplatePath($this->params['t']) . $this->params['t'] . '.ctp')) {
			$template = $this->params['t'];
		}

		ob_start();
		include $this->_getTemplatePath($template) . $template . '.ctp';
		$conf = ob_get_contents();
		ob_end_clean();

		$this->out($conf);
		
		$this->createFile(ROOT . DS . APP_DIR . DS . 'config' . DS . 'vhost' . DS . $domain . '.conf', $conf);
	}

	protected function _getTemplatePath($template='') {
		$app_vhost = ROOT . DS . APP_DIR . DS . 'config' . DS . 'vhost' . DS;
		if (is_file($app_vhost . $template . '.ctp')) {
			// app config/vhost/{template}.ctp
			return $app_vhost;
		} else {
			// app config/vhost/{template}.ctp
			return dirname(dirname(dirname(__FILE__))) . DS . 'config' . DS . 'vhost' . DS;
		}
	}
}
