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
		/*** vars used in the template START ***/
		$domain  = $this->args[0];
		$webroot = WWW_ROOT;
		$logpath = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'logs' . DS;
		/*** vars used in the template END ***/

		$template = 'apache';
		if (!empty($this->params['template']) && is_file($this->_getTemplatePath($this->params['template']) . $this->params['template'] . '.ctp')) {
			$template = $this->params['template'];
		}

		ob_start();
		include $this->_getTemplatePath($template) . $template . '.ctp';
		$conf = ob_get_contents();
		ob_end_clean();

		$this->out($conf);

		$this->createFile(ROOT . DS . APP_DIR . DS . 'Config' . DS . 'Vhost' . DS . $domain . '.conf', $conf);
	}

	protected function _getTemplatePath($template='') {
		$app_vhost = ROOT . DS . APP_DIR . DS . 'Config' . DS . 'Vhost' . DS;
		if (is_file($app_vhost . $template . '.ctp')) {
			// app Config/Vhost/{template}.ctp
			return $app_vhost;
		} else {
			// app Config/Vhost/{template}.ctp
			return dirname(dirname(dirname(__FILE__))) . DS . 'Config' . DS . 'Vhost' . DS;
		}
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->addOption('template', array(
			'short' => 't',
			'help' => 'template: apache | nginx',
			'default' => 'apache',
		));
		return $parser;
	}
}
