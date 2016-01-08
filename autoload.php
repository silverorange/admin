<?php

namespace Silverorange\Autoloader;

$package = new Package('silverorange/admin');

$package->addRule(
	new Rule(
		'pages',
		'Admin',
		array(
			'Approval',
			'Confirmation',
			'Delete',
			'Edit',
			'Index',
			'Order',
			'Page',
			'Search',
			'Server',
		)
	)
);

$package->addRule(new Rule('layouts', 'Admin', 'Layout'));
$package->addRule(new Rule('exceptions', 'Admin', 'Exception'));

$package->addRule(
	new Rule(
		'dataobjects',
		'Admin',
		array(
			'Component',
			'Group',
			'Section',
			'SubComponent',
			'UserHistory',
			'User',
			'Wrapper',
		)
	)
);

$package->addRule(new Rule('', 'Admin'));

Autoloader::addPackage($package);

?>
