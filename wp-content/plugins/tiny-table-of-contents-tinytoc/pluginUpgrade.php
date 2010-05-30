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
    'tinytoc_chapter_styling',
    'tinytoc_settings_info'
);

$autoload = array(
    'yes',
    'no',
    'no',
    'no',
    'no',
    'no',
    'no',
    'no'
);

$config = tinyConfig::getInstance()->get($options);

$settings = array();
$settings[] = isset($config->tinytoc_settings_enabled) ? $config->tinytoc_settings_enabled : '1';

$config = new stdClass();

$config->title = isset($config->tinytoc_settings_header->title) ?
    $config->tinytoc_settings_header->title : 'Table of content';
$config->before = isset($config->tinytoc_settings_header->before) ?
    $config->tinytoc_settings_header->before : '<h3 id="tinyTOC">';
$config->after = isset($config->tinytoc_settings_header->after) ?
    $config->tinytoc_settings_header->after : '</h3>';
$config->css = isset($config->tinytoc_settings_header->css) ?
    $config->tinytoc_settings_header->css : '';

$settings[] = $config;

$config = new stdClass();

$config->maxLevelNum = isset($config->tinytoc_settings_general->maxLevelNum) ?
    $config->tinytoc_settings_general->maxLevelNum : 3;
$config->useBackToTop = isset($config->tinytoc_settings_general->useBackToTop) ?
    $config->tinytoc_settings_general->useBackToTop : true;
$config->useGoTo = isset($config->tinytoc_settings_general->useGoTo) ?
    $config->tinytoc_settings_general->useGoTo : true;
$config->priority = isset($config->tinytoc_settings_general->priority) ?
    $config->tinytoc_settings_general->priority : 8;
$config->removeWhenNotUsed = isset($config->tinytoc_settings_general->removeWhenNotUsed) ?
    $config->tinytoc_settings_general->removeWhenNotUsed : true;
$config->tocOnAllPages = isset($config->tinytoc_settings_general->tocOnAllPages) ?
    $config->tinytoc_settings_general->tocOnAllPages : false;

$settings[] = $config;

$config = new stdClass();

$config->parsePage = isset($config->tinytoc_settings_parse->parsePage) ?
    $config->tinytoc_settings_parse->parsePage : true;
$config->parsePost = isset($config->tinytoc_settings_parse->parsePost) ?
    $config->tinytoc_settings_parse->parsePost : true;
$config->parseHomePage = isset($config->tinytoc_settings_parse->parseHomePage) ?
    $config->tinytoc_settings_parse->parseHomePage : true;
$config->parseSearch = isset($config->tinytoc_settings_parse->parseSearch) ?
    $config->tinytoc_settings_parse->parseSearch : true;
$config->parseFeed = isset($config->tinytoc_settings_parse->parseFeed) ?
    $config->tinytoc_settings_parse->parseFeed : true;
$config->parseCategoryArchive = isset($config->tinytoc_settings_parse->parseCategoryArchive) ?
    $config->tinytoc_settings_parse->parseCategoryArchive : true;
$config->parseDateArchive = isset($config->tinytoc_settings_parse->parseDateArchive) ?
    $config->tinytoc_settings_parse->parseDateArchive : true;
$config->parseAnyArchive = isset($config->tinytoc_settings_parse->parseAnyArchive) ?
    $config->tinytoc_settings_parse->parseAnyArchive : true;
$config->pageExclude = isset($config->tinytoc_settings_parse->pageExclude) ?
    $config->tinytoc_settings_parse->pageExclude : array();
$config->postExclude = isset($config->tinytoc_settings_parse->postExclude) ?
    $config->tinytoc_settings_parse->postExclude : array();

$settings[] = $config;

$config = new stdClass();

$config->html = isset($config->tinytoc_settings_backtotop->html) ?
    $config->tinytoc_settings_backtotop->text : ' <small><a href="#tinyTOC">Top</a></small>';
$config->css = isset($config->tinytoc_settings_backtotop->css) ?
    $config->tinytoc_settings_backtotop->css : '';

$settings[] = $config;

$config = new stdClass();

$config->startList = isset($config->tinytoc_settings_tocstyle->startList) ?
    $config->tinytoc_settings_tocstyle->startList : '<ul>';
$config->endList = isset($config->tinytoc_settings_tocstyle->endList) ?
    $config->tinytoc_settings_tocstyle->endList : '</ul>';
$config->startItem = isset($config->tinytoc_settings_tocstyle->startItem) ?
    $config->tinytoc_settings_tocstyle->startItem : '<li>';
$config->endItem = isset($config->tinytoc_settings_tocstyle->endItem) ?
    $config->tinytoc_settings_tocstyle->endItem : '</li>';
$config->css = isset($config->tinytoc_settings_tocstyle->css) ?
    $config->tinytoc_settings_tocstyle->css : '';

$settings[] = $config;

$config = new stdClass();

$config->useChapterLevelStyling = isset($config->tinytoc_chapter_styling->useChapterLevelStyling) ?
    $config->tinytoc_settings_tocstyle->startList : false;
$config->stripExistingTags = isset($config->tinytoc_chapter_styling->stripExistingTags) ?
    $config->tinytoc_settings_tocstyle->endList : false;
$config->levelStyleStart = isset($config->tinytoc_chapter_styling->levelStyleStart) ?
    $config->tinytoc_settings_tocstyle->startItem : array(
        1   => '',
        2   => '',
        3   => ''
    );
$config->levelStyleEnd = isset($config->tinytoc_chapter_styling->levelStyleEnd) ?
    $config->tinytoc_settings_tocstyle->endItem : array(
        1   => '',
        2   => '',
        3   => ''
    );

$settings[] = $config;

$config = new stdClass();

$config->name = 'Tiny Table Of Content - TinyTOC';
$config->version = '0.12.31';
$config->home = 'http://php4every1.com/scripts/tiny-table-of-contents-wordpress-plugin/';
$config->author = 'Marijan Šuflaj';
$config->email = 'msufflaj32@gmail.com';
$config->authorHome = 'http://www.php4every1.com';

$settings[] = $config;

tinyConfig::getInstance()->delete($keys);

tinyConfig::getInstance()->create($keys, $settings, $autoload, 'no');