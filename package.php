<?php

require_once 'PEAR/PackageFileManager2.php';

$version = '1.3.69';
$notes = <<<EOT
see ChangeLog
EOT;

$description =<<<EOT
Admin is a Swat based framework for building administration sites.

* An OO-style API
* A set of user-interface widgets
EOT;

$package = new PEAR_PackageFileManager2();
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$result = $package->setOptions(
	array(
		'filelistgenerator' => 'svn',
		'simpleoutput'      => true,
		'baseinstalldir'    => '/',
		'packagedirectory'  => './',
		'dir_roles'         => array(
			'Admin'        => 'php',
			'locale'       => 'data',
			'www'          => 'data',
			'dependencies' => 'data',
			'/'            => 'data'
		),
	)
);

$package->setPackage('Admin');
$package->setSummary('Swat based adminstration framework');
$package->setDescription($description);
$package->setChannel('pear.silverorange.com');
$package->setPackageType('php');
$package->setLicense('LGPL', 'http://www.gnu.org/copyleft/lesser.html');

$package->setReleaseVersion($version);
$package->setReleaseStability('stable');
$package->setAPIVersion('0.0.1');
$package->setAPIStability('stable');
$package->setNotes($notes);

$package->addIgnore('package.php');

$package->addMaintainer('lead', 'nrf', 'Nathan Fredrickson', 'nathan@silverorange.com');
$package->addMaintainer('lead', 'gauthierm', 'Mike Gauthier', 'mike@silverorange.com');

$package->addReplacement('Admin/Admin.php', 'pear-config', '@DATA-DIR@', 'data_dir');

$package->setPhpDep('5.1.5');
$package->setPearinstallerDep('1.4.0');
$package->addPackageDepWithChannel('required', 'Swat', 'pear.silverorange.com', '1.4.57');
$package->addPackageDepWithChannel('required', 'Site', 'pear.silverorange.com', '1.4.84');
$package->addPackageDepWithChannel('required', 'XML_RPCAjax', 'pear.silverorange.com', '1.0.9');
$package->addPackageDepWithChannel('required', 'Net_Notifier', 'pear.silverorange.com', '0.1.0');
$package->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
	$package->writePackageFile();
} else {
	$package->debugPackageFile();
}

?>
