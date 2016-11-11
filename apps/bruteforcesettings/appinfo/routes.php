<?php

return [
	[ 'name' => 'IPWhitelist#getAll', 'url' => '/ipwhitelist', 'verb' => 'GET' ],
	[ 'name' => 'IPWhitelist#add', 'url' => '/ipwhitelist', 'verb' => 'POST' ],
	[ 'name' => 'IPWhitelist#remove', 'url' => '/ipwhitelist/{id}', 'verb' => 'DELETE' ],
];
