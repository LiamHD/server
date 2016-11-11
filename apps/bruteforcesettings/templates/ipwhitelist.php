<?php

script('core', [
	'oc-backbone-webdav',
]);
script('bruteforcesettings', [
	'IPWhitelist',
	'IPWhitelistModel',
	'IPWhitelistCollection',
	'IPWhitelistView',
]);

/** @var \OCP\IL10N $l */
?>
<form id="IPWhiteList" class="section">
	<h2><?php p($l->t('Brute force ip whitelist')); ?></h2>

	<table>
		<tbody id="whitelist-list">

		</tbody>
	</table>

	<input type="text" name="whitelist_ip" id="whitelist_ip" placeholder="<?php p($l->t('1.2.3.4')); ?>" style="width: 200px;" />/
	<input type="number" id="whitelist_mask" name="whitelist_mask" placeholder="<?php p($l->t('24')); ?>" style="width: 50px;">
	<input type="button" id="whitelist_submit" value="<?php p($l->t('Add')); ?>">
</form>
