<?php
/**
 * @author Marijan Šuflaj <msufflaj32@gmail.com>
 * @link http://www.php4every1.com
 */

//Disable direct view.
if (!defined('IN_PLUGIN'))
    die('You can not access this file directly.');

//Update settings
$keys = array(
    'tinytoc_settings_enabled',
    'tinytoc_settings_header',
    'tinytoc_settings_general',
    'tinytoc_settings_parse',
    'tinytoc_settings_backtotop',
    'tinytoc_settings_tocstyle',
    'tinytoc_chapter_styling'
);

$settings = array();
$settings[] = '1';

$config = new stdClass();

$config->title = 'Table of content';
$config->before = '<h3 id="tinyTOC">';
$config->after = '</h3>';
$config->css = '';

$settings[] = $config;

$config = new stdClass();

$config->maxLevelNum = 3;
$config->useBackToTop = true;
$config->useGoTo = true;
$config->priority = 8;

$settings[] = $config;

$config = new stdClass();

if (isset($_POST['parseAnyArchive'])) {
	$_POST['parseCategory'] = '1';
	$_POST['parseDate'] = '1';
}

$config->parsePage = true;
$config->parsePost = true;
$config->parseHomePage = true;
$config->parseSearch = true;
$config->parseFeed = true;
$config->parseCategoryArchive = true;
$config->parseDateArchive = true;
$config->parseAnyArchive = true;
$config->pageExclude = array();
$config->postExclude = array();

$settings[] = $config;

$config = new stdClass();

$config->image = '';
$config->text = ' <small><a href="#tinyTOC">Top</a></small>';
$config->css = '';

$settings[] = $config;

$config = new stdClass();

$config->useChapterLevelStyling = false;
$config->stripExistingTags = false;
$config->levelStyleStart = array(
    1   => '',
    2   => '',
    3   => ''
);
$config->levelStyleEnd = array(
    1   => '',
    2   => '',
    3   => ''
);

$settings[] = $config;

tinyConfig::getInstance()->update($keys, $settings);
?>
<div class="updated"><p><strong>Options reseted.</strong></p></div>