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
$settings[] = (isset($_POST['tinytocEnabled']) ? '1' : '0');

$config = new stdClass();

$config->title = (string) $_POST['headerTitle'];
$config->before = (string) $_POST['headerBefore'];
$config->after = (string) $_POST['headerAfter'];
$config->css = (string) $_POST['headerCss'];

$settings[] = $config;

$config = new stdClass();

$config->maxLevelNum = (int) $_POST['maxLevelNum'];
$config->useBackToTop = (isset($_POST['useBackToTop']) ? true : false);
$config->useGoTo = (isset($_POST['useGoTo']) ? true : false);
$config->priority = (int) $_POST['priority'];
$config->removeWhenNotUsed = (isset($_POST['removeWhenNotUsed']) ? true : false);
$config->tocOnAllPages = (isset($_POST['tocOnAllPages']) ? true : false);

$settings[] = $config;

$config = new stdClass();

if (isset($_POST['parseAnyArchive'])) {
    $_POST['parseCategory'] = '1';
    $_POST['parseDate'] = '1';
}

$config->parsePage = (isset($_POST['parsePost']) ? true : false);
$config->parsePost = (isset($_POST['parsePage']) ? true : false);
$config->parseHomePage = (isset($_POST['parseHomePage']) ? true : false);
$config->parseSearch = (isset($_POST['parseSearch']) ? true : false);
$config->parseFeed = (isset($_POST['parseFeed']) ? true : false);
$config->parseCategoryArchive = (isset($_POST['parseCategory']) ? true : false);
$config->parseDateArchive = (isset($_POST['parseDate']) ? true : false);
$config->parseAnyArchive = (isset($_POST['parseAnyArchive']) ? true : false);
$config->pageExclude = (array) $_POST['excludePages'];
$config->postExclude = (array) $_POST['excludePosts'];

$settings[] = $config;

$config = new stdClass();

$config->html = (string) $_POST['backToTopHtml'];
$config->css = (string) $_POST['backToTopCss'];

$settings[] = $config;

$config = new stdClass();

$config->startList = (string) $_POST['tocStartList'];
$config->endList = (string) $_POST['tocEndList'];
$config->startItem = (string) $_POST['tocStartItem'];
$config->endItem = (string) $_POST['tocEndItem'];
$config->css = (string) $_POST['tocCss'];

$settings[] = $config;

$config = new stdClass();

$config->useChapterLevelStyling = (isset($_POST['useChapterLevelStyling']) ? true : false);
$config->stripExistingTags = (isset($_POST['stripExistingTags']) ? true : false);
$config->levelStyleStart = $_POST['levelStyleStart'];
$config->levelStyleEnd = $_POST['levelStyleEnd'];

$settings[] = $config;

tinyConfig::getInstance()->update($keys, $settings);
?>
<div class="updated"><p><strong>Options updated.</strong></p></div>