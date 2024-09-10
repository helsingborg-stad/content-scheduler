<?php

/**
 * Plugin Name:       Content Scheduler
 * Plugin URI:        https://github.com/helsingborg-stad/content-scheduler
 * Description:       Interface for post publishing and unpublisching schedulation
 * Version: 3.1.3
 * Author:            Kristoffer Svanmark, Sebastian Thulin
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

// Autoload from plugin
if (file_exists(CONTENTSCHEDULER_PATH . 'vendor/autoload.php')) {
	require_once CONTENTSCHEDULER_PATH . 'vendor/autoload.php';
}
require_once CONTENTSCHEDULER_PATH . 'Public.php';


// Acf auto import and export
add_action('plugins_loaded', function () {
		$acfExportManager = new AcfExportManager\AcfExportManager();
		$acfExportManager->setTextdomain('content-scheduler');
		$acfExportManager->setExportFolder(CONTENTSCHEDULER_PATH . 'source/php/AcfFields/');
		$acfExportManager->autoExport(array());
		$acfExportManager->import();
});

// Start application
new ContentScheduler\App();
