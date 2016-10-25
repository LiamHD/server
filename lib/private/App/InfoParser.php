<?php
/**
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @copyright Copyright (c) 2016, Lukas Reschke <lukas@statuscode.ch>
 *
 * @author Andreas Fischer <bantu@owncloud.com>
 * @author Christoph Wurst <christoph@owncloud.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\App;

use OCP\ICache;

class InfoParser {
	/** @var \OCP\ICache|null */
	private $cache;

	/**
	 * @param ICache|null $cache
	 */
	public function __construct(ICache $cache = null) {
		$this->cache = $cache;
	}

	/**
	 * @param string $file the xml file to be loaded
	 * @return null|array where null is an indicator for an error
	 */
	public function parse($file) {
		if (!file_exists($file)) {
			return null;
		}

		if(!is_null($this->cache)) {
			$fileCacheKey = $file . filemtime($file);
			if ($cachedValue = $this->cache->get($fileCacheKey)) {
				return json_decode($cachedValue, true);
			}
		}

		libxml_use_internal_errors(true);
		$loadEntities = libxml_disable_entity_loader(false);
		$xml = simplexml_load_file($file);

		libxml_disable_entity_loader($loadEntities);
		if ($xml === false) {
			libxml_clear_errors();
			return null;
		}
		$array = $this->xmlToArray($xml);

		if (is_null($array)) {
			return null;
		}

		if (!array_key_exists('info', $array)) {
			$array['info'] = [];
		}
		if (!array_key_exists('remote', $array)) {
			$array['remote'] = [];
		}
		if (!array_key_exists('public', $array)) {
			$array['public'] = [];
		}
		if (!array_key_exists('types', $array)) {
			$array['types'] = [];
		}
		if (!array_key_exists('repair-steps', $array)) {
			$array['repair-steps'] = [];
		}
		if (!array_key_exists('install', $array['repair-steps'])) {
			$array['repair-steps']['install'] = [];
		}
		if (!array_key_exists('pre-migration', $array['repair-steps'])) {
			$array['repair-steps']['pre-migration'] = [];
		}
		if (!array_key_exists('post-migration', $array['repair-steps'])) {
			$array['repair-steps']['post-migration'] = [];
		}
		if (!array_key_exists('live-migration', $array['repair-steps'])) {
			$array['repair-steps']['live-migration'] = [];
		}
		if (!array_key_exists('uninstall', $array['repair-steps'])) {
			$array['repair-steps']['uninstall'] = [];
		}
		if (!array_key_exists('background-jobs', $array)) {
			$array['background-jobs'] = [];
		}
		if (!array_key_exists('two-factor-providers', $array)) {
			$array['two-factor-providers'] = [];
		}
		if (!array_key_exists('commands', $array)) {
			$array['commands'] = [];
		}
		if (!array_key_exists('activity', $array)) {
			$array['activity'] = [];
		}
		if (!array_key_exists('filters', $array['activity'])) {
			$array['activity']['filters'] = [];
		}
		if (!array_key_exists('settings', $array['activity'])) {
			$array['activity']['settings'] = [];
		}

		if (array_key_exists('types', $array)) {
			if (is_array($array['types'])) {
				foreach ($array['types'] as $type => $v) {
					unset($array['types'][$type]);
					if (is_string($type)) {
						$array['types'][] = $type;
					}
				}
			} else {
				$array['types'] = [];
			}
		}
		if (isset($array['repair-steps']['install']['step']) && is_array($array['repair-steps']['install']['step'])) {
			$array['repair-steps']['install'] = $array['repair-steps']['install']['step'];
		}
		if (isset($array['repair-steps']['pre-migration']['step']) && is_array($array['repair-steps']['pre-migration']['step'])) {
			$array['repair-steps']['pre-migration'] = $array['repair-steps']['pre-migration']['step'];
		}
		if (isset($array['repair-steps']['post-migration']['step']) && is_array($array['repair-steps']['post-migration']['step'])) {
			$array['repair-steps']['post-migration'] = $array['repair-steps']['post-migration']['step'];
		}
		if (isset($array['repair-steps']['live-migration']['step']) && is_array($array['repair-steps']['live-migration']['step'])) {
			$array['repair-steps']['live-migration'] = $array['repair-steps']['live-migration']['step'];
		}
		if (isset($array['repair-steps']['uninstall']['step']) && is_array($array['repair-steps']['uninstall']['step'])) {
			$array['repair-steps']['uninstall'] = $array['repair-steps']['uninstall']['step'];
		}
		if (isset($array['background-jobs']['job']) && is_array($array['background-jobs']['job'])) {
			$array['background-jobs'] = $array['background-jobs']['job'];
		}
		if (isset($array['commands']['command']) && is_array($array['commands']['command'])) {
			$array['commands'] = $array['commands']['command'];
		}
		if (isset($array['activity']['filters']['filter']) && is_array($array['activity']['filters']['filter'])) {
			$array['activity']['filters'] = $array['activity']['filters']['filter'];
		}
		if (isset($array['activity']['settings']['setting']) && is_array($array['activity']['settings']['setting'])) {
			$array['activity']['settings'] = $array['activity']['settings']['setting'];
		}

		if(!is_null($this->cache)) {
			$this->cache->set($fileCacheKey, json_encode($array));
		}
		return $array;
	}

	/**
	 * @param \SimpleXMLElement $xml
	 * @return array
	 */
	function xmlToArray($xml) {
		if (!$xml->children()) {
			return (string)$xml;
		}

		$array = [];
		foreach ($xml->children() as $element => $node) {
			$totalElement = count($xml->{$element});

			if (!isset($array[$element])) {
				$array[$element] = $totalElement > 1 ? [] : "";
			}
			/** @var \SimpleXMLElement $node */
			// Has attributes
			if ($attributes = $node->attributes()) {
				$data = [
					'@attributes' => [],
				];
				if (!count($node->children())){
					$value = (string)$node;
					if (!empty($value)) {
						$data['@value'] = (string)$node;
					}
				} else {
					$data = array_merge($data, $this->xmlToArray($node));
				}
				foreach ($attributes as $attr => $value) {
					$data['@attributes'][$attr] = (string)$value;
				}

				if ($totalElement > 1) {
					$array[$element][] = $data;
				} else {
					$array[$element] = $data;
				}
				// Just a value
			} else {
				if ($totalElement > 1) {
					$array[$element][] = $this->xmlToArray($node);
				} else {
					$array[$element] = $this->xmlToArray($node);
				}
			}
		}

		return $array;
	}
}
