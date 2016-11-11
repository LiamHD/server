<?php

namespace OCA\BruteForceSettings\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class IPWhitelist implements ISettings {

	public function getForm() {
		return new TemplateResponse('bruteforcesettings', 'ipwhitelist');
	}

	public function getSection() {
		return 'security';
	}

	public function getPriority() {
		return 50;
	}
}
