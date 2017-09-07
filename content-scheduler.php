<?php

/**
 * Plugin Name:       Content Scheduler
 * Plugin URI:        https://github.com/helsingborg-stad/content-scheduler
 * Description:       Interface for post publishing and unpublisching schedulation
 * Version:           1.0.0
 * Author:            Kristoffer Svanmark
 * Author URI:        https://github.com/helsingborg-stad
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       content-scheduler
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('CONTENTSCHEDULER_PATH', plugin_dir_path(__FILE__));
define('CONTENTSCHEDULER_URL', plugins_url('', __FILE__));
define('CONTENTSCHEDULER_TEMPLATE_PATH', CONTENTSCHEDULER_PATH . 'templates/');

load_plugin_textdomain('content-scheduler', false, plugin_basename(dirname(__FILE__)) . '/languages');

require_once CONTENTSCHEDULER_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once CONTENTSCHEDULER_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new ContentScheduler\Vendor\Psr4ClassLoader();
$loader->addPrefix('ContentScheduler', CONTENTSCHEDULER_PATH);
$loader->addPrefix('ContentScheduler', CONTENTSCHEDULER_PATH . 'source/php/');
$loader->register();

// Acf auto import and export
add_action('plugins_loaded', function () {
	$acfExportManager = new AcfExportManager\AcfExportManager();
	$acfExportManager->setTextdomain('content-scheduler');
	$acfExportManager->setExportFolder(CONTENTSCHEDULER_PATH . 'source/php/AcfFields/');
	$acfExportManager->autoExport(array(
	    'content-scheduler-options' => 'group_59b0f1288d51b',
	));
	$acfExportManager->import();
});

// Start application
new ContentScheduler\App();
