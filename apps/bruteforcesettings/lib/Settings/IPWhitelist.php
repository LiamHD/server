<?php

namespace OC\Settings\Admin;

use OCP\Settings\ISettings;

class IPWhitelist implements ISettings {

	public function getForm() {

	}

	public function getSection() {
		return 'security';
	}

	public function getPriority() {
		return 50;
	}
}
