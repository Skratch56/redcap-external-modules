<?php
namespace ExternalModules;

require_once '../../classes/ExternalModules.php';

$module = $_POST['module'];

if (empty($module)) {
	echo 'You must specify a module to remove';
	return;
}

// TODO - Remove this this mocked module is no longer listed.
if ($module == 'doggy-daycare') {
	die('success');
}

# TODO - need better security here (perhaps check for '..' or '/')
ExternalModules::remove($module);

echo 'success';