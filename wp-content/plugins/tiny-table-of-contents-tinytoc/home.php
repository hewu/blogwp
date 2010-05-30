<?php
/**
 * @author Marijan Šuflaj <msufflaj32@gmail.com>
 * @link http://www.php4every1.com
 */

//Disable direct view.
if (!defined('IN_PLUGIN'))
    die('You can not access this file directly.');

$config = tinyConfig::getInstance()->get('');
?>
<div class="wrap">
    <h3>Plugin summary</h3>

    <h4>Info</h4>
    <table class="widefat" style="width: 500px">
        <tbody>
            <tr>
                <td width="49%">
                    Name:
                </td>
                <td>
                    <a href="<?php echo $config->tinytoc_settings_info->home; ?>"><?php echo $config->tinytoc_settings_info->name; ?></a>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Version:
                </td>
                <td>
                    <?php echo $config->tinytoc_settings_info->version; ?>
                </td>
            </tr>
            <tr>
                <td>
                   Author:
                </td>
                <td>
                    <a href="<?php echo $config->tinytoc_settings_info->authorHome; ?>"><?php echo $config->tinytoc_settings_info->author; ?></a>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    E-mail:
                </td>
                <td>
                    <?php echo $config->tinytoc_settings_info->email; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <h4>Style chapter level headings</h4>

    <table class="widefat" style="width: 500px">
        <thead>
            <tr>
                <th width="49%">
                    Option
                </th>
                <th>
                    Value
                </th>
            </tr>
        </thead>
        <tbody id="chapterLevelStyleBody">
            <tr>
                <td>
                    Use chapter level styling:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_chapter_styling->useChapterLevelStyling === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Strip existing tags:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_chapter_styling->stripExistingTags === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <?php if ((bool) $config->tinytoc_chapter_styling->useChapterLevelStyling === true) : ?>
            <?php for ($i = 1; $i <= $config->tinytoc_settings_general->maxLevelNum; $i++) : ?>
            <tr>
                <td>
                    Level <?php echo $i; ?> start:
                </td>
                <td>
                    <?php echo isset($config->tinytoc_chapter_styling->levelStyleStart[$i]) ? htmlentities($config->tinytoc_chapter_styling->levelStyleStart[$i]) : ''; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Level <?php echo $i; ?> end:
                </td>
                <td>
                   <?php echo isset($config->tinytoc_chapter_styling->levelStyleEnd[$i]) ? htmlentities($config->tinytoc_chapter_styling->levelStyleEnd[$i]) : ''; ?>
                </td>
            </tr>
            <?php endfor; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h4>General</h4>

    <table class="widefat" style="width: 500px">
        <thead>
            <tr>
                <th width="49%">
                    Option
                </th>
                <th>
                    Value
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Enabled:
                </td>
                <td>
                    <?php echo ($config->tinytoc_settings_enabled === '1') ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Maximum level number:
                </td>
                <td>
                    <?php echo $config->tinytoc_settings_general->maxLevelNum; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Use "Back to top" button:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_general->useBackToTop === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Priority:
                </td>
                <td>
                    <?php echo $config->tinytoc_settings_general->priority; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Use "Go to" feature:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_general->useGoTo === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Remove when not used:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_general->removeWhenNotUsed === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr>
                <td>
                    TOC on all pages:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_general->tocOnAllPages === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <h4>Header</h4>
    <table class="widefat" style="width: 500px">
        <thead>
            <tr>
                <th width="49%">
                    Option
                </th>
                <th>
                    Value
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Title:
                </td>
                <td>
                    <?php echo $config->tinytoc_settings_header->title; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Html before title:
                </td>
                <td>
                    <?php echo htmlentities($config->tinytoc_settings_header->before); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Html after title:
                </td>
                <td>
                    <?php echo htmlentities($config->tinytoc_settings_header->after); ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Header css style:
                </td>
                <td>
                    <?php echo htmlentities($config->tinytoc_settings_header->css); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <h4>Back to top</h4>
    <table class="widefat" style="width: 500px">
        <thead>
                <tr>
                    <th width="49%">
                        Option
                    </th>
                    <th>
                        Value
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Html:
                    </td>
                    <td>
                        <?php echo htmlentities($config->tinytoc_settings_backtotop->html); ?>
                    </td>
                </tr>
                <tr class="alternate">
                    <td>
                        Css:
                    </td>
                    <td>
                        <?php echo htmlentities($config->tinytoc_settings_backtotop->css); ?>
                    </td>
                </tr>
            </tbody>
    </table>

    <h4>Table of contents style</h4>
    <table class="widefat" style="width: 500px">
        <thead>
            <tr>
                <th width="49%">
                    Option
                </th>
                <th>
                    Value
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Start list:
                </td>
                <td>
                    <?php echo htmlentities($config->tinytoc_settings_tocstyle->startList); ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    End list:
                </td>
                <td>
                    <?php echo htmlentities($config->tinytoc_settings_tocstyle->endList); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Start list item:
                </td>
                <td>
                    <?php echo htmlentities($config->tinytoc_settings_tocstyle->startItem); ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    End list item:
                </td>
                <td>
                    <?php echo htmlentities($config->tinytoc_settings_tocstyle->endItem); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Css:
                </td>
                <td>
                    <?php echo htmlentities($config->tinytoc_settings_tocstyle->css); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <h4>Display on</h4>
    <table class="widefat" style="width: 500px">
        <thead>
            <tr>
                <th width="49%">
                    Option
                </th>
                <th>
                    Value
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Post:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_parse->parsePost === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Page:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_parse->parsePage === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Home page:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_parse->parseHomePage === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Search:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_parse->parseSearch === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Feed:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_parse->parseFeed === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Category archive:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_parse->parseCategoryArchive === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Date archive:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_parse->parseDateArchive === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Any archive:
                </td>
                <td>
                    <?php echo ((bool) $config->tinytoc_settings_parse->parseAnyArchive === true) ? 'true' : 'false'; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Exclude pages:
                </td>
                <td>
                    <?php echo implode(', ', $config->tinytoc_settings_parse->pageExclude); ?>
                </td>
            </tr>
            <tr class="alternate">
                <td>
                    Exclude posts:
                </td>
                <td>
                    <?php echo implode(', ', $config->tinytoc_settings_parse->postExclude); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>