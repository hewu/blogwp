<?php
/**
 * Retrieves and creates the wp-config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the wp-config.php to be created using this page.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * We are installing.
 *
 * @package WordPress
 */
define('WP_INSTALLING', true);

/**
 * Disable error reporting
 *
 * Set this to error_reporting( E_ALL ) or error_reporting( E_ALL | E_STRICT ) f
or debugging
 */
error_reporting(0);

/**#@+
 * These three defines are required to allow us to use require_wp_db() to load
 * the database class while being wp-content/db.php aware.
 * @ignore
 */
define('ABSPATH', dirname(dirname(__FILE__)).'/');
define('WPINC', 'wp-includes');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
/**#@-*/

require_once(ABSPATH . WPINC . '/compat.php');
require_once(ABSPATH . WPINC . '/functions.php');
require_once(ABSPATH . WPINC . '/classes.php');

if (!file_exists(ABSPATH . 'wp-config-sample.php'))
	wp_die('提示！未能检测到 wp-config-sample.php 文件，请确认根目录存在此文件或重新上传。');

$configFile = file(ABSPATH . 'wp-config-sample.php');

// Check if wp-config.php has been created
if (file_exists(ABSPATH . 'wp-config.php'))
	wp_die("<p>'wp-config.php'文件已存在。如果您想更改 wp-config.php 内已有的设定，请先删除它，本向导会重新创建 wp-config.php 。<a href='install.php'>重试</a>。</p>");

// Check if wp-config.php exists above the root directory but is not part of another install
if (file_exists(ABSPATH . '../wp-config.php') && ! file_exists(ABSPATH . '../wp-settings.php'))
	wp_die("<p>'wp-config.php'已存在于更高一级的目录内。如果您想更改 wp-config.php 内已有的设定，请先删除它，本向导会重新创建wp-config.php。<a href='install.php'>重试</a>。</p>");

if ( version_compare( '4.3', phpversion(), '>' ) )
	wp_die( sprintf( /*WP_I18N_OLD_PHP*/'您服务器的PHP版本为 %s 但 WordPress 至少需要 4.3.'/*/WP_I18N_OLD_PHP*/, phpversion() ) );

if ( !extension_loaded('mysql') && !file_exists(ABSPATH . 'wp-content/db.php') )
	wp_die( /*WP_I18N_OLD_MYSQL*/'您的PHP似乎没有 MySQL 拓展。'/*/WP_I18N_OLD_MYSQL*/ );

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

/**
 * Display setup wp-config.php file header.
 *
 * @ignore
 * @since 2.3.0
 * @package WordPress
 * @subpackage Installer_WP_Config
 */
function display_header() {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WordPress &rsaquo; Setup Configuration File</title>
<link rel="stylesheet" href="css/install.css" type="text/css" />

</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
	case 0:
		display_header();
?>

<p>欢迎来到 WordPress 的世界！正式开始之前，我们需要一些关于数据库的信息。请确认您已经拥有以下信息：</p>
<ol>
	<li>数据库名称</li>
	<li>数据库用户名</li>
	<li>数据库密码</li>
	<li>数据库主机地址</li>
	<li>数据表前缀（如果您需要在同一数据库内安装多个 WordPress 的话）</li>
</ol>
<p><strong>如果无法进入下一步，别着急。此向导的目的在于创建 Wordpress 的配置文件，所以您还可以直接用文本编辑器打开 <code>wp-config-sample.php</code>，根据提示填写相应信息，然后保存并将它重命名为 <code>wp-config.php</code>。</strong></p>
<p>正常情况下，您的空间商会告知数据库的有关信息。如果您不太清楚，请先联系您的空间提供商。如果已经准备好了 &hellip;</p>


<p class="step"><a href="setup-config.php?step=1" class="button">开始!</a></p>
<?php
	break;

	case 1:
		display_header();
	?>
<form method="post" action="setup-config.php?step=2">
	<p>您应该在下面的表单中填入对应的数据库信息，不确定的项目请联系您的空间提供商确认。 </p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="dbname">数据库名称</label></th>
			<td><input name="dbname" id="dbname" type="text" size="25" value="wordpress" /></td>
			<td>用于存储 WordPress 数据的数据库名称。</td>
		</tr>
		<tr>
			<th scope="row"><label for="uname">数据库用户名</label></th>
			<td><input name="uname" id="uname" type="text" size="25" value="username" /></td>
			<td>您的 MySQL 用户名</td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">数据库密码</label></th>
			<td><input name="pwd" id="pwd" type="text" size="25" value="password" /></td>
			<td>...以及 MySQL 密码</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost">数据库地址</label></th>
			<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
			<td>大部分主机无需修改。</td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix">数据表前缀</label></th>
			<td><input name="prefix" id="prefix" type="text" id="prefix" value="wp_" size="25" /></td>
			<td>如果有在同一数据库内安装多个 WordPress 的需求请更改此项。</td>
		</tr>
	</table>
	<p class="step"><input name="submit" type="submit" value="提交" class="button" /></p>
</form>
<?php
	break;

	case 2:
	$dbname  = trim($_POST['dbname']);
	$uname   = trim($_POST['uname']);
	$passwrd = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	$prefix  = trim($_POST['prefix']);
	if (empty($prefix)) $prefix = 'wp_';

	// Test the db connection.
	/**#@+
	 * @ignore
	 */
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $passwrd);
	define('DB_HOST', $dbhost);
	/**#@-*/

	// We'll fail here if the values are no good.
	require_wp_db();
	if ( !empty($wpdb->error) )
		wp_die($wpdb->error->get_error_message());

	foreach ($configFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('DB_NAME'":
				$configFile[$line_num] = str_replace("putyourdbnamehere", $dbname, $line);
				break;
			case "define('DB_USER'":
				$configFile[$line_num] = str_replace("'usernamehere'", "'$uname'", $line);
				break;
			case "define('DB_PASSW":
				$configFile[$line_num] = str_replace("'yourpasswordhere'", "'$passwrd'", $line);
				break;
			case "define('DB_HOST'":
				$configFile[$line_num] = str_replace("localhost", $dbhost, $line);
				break;
			case '$table_prefix  =':
				$configFile[$line_num] = str_replace('wp_', $prefix, $line);
				break;
		}
	}
	if ( ! is_writable(ABSPATH) ) :
		display_header();
?>
<p>抱歉，无法写入<code>wp-config.php</code>文件。</p>
<p>您可以手动创建<code>wp-config.php</code>文件，然后复制粘贴下面的文本过去。</p>
<textarea cols="90" rows="15"><?php
		foreach( $configFile as $line ) {
			echo htmlentities($line);
		}
?></textarea>
<p>完成后，点击“开始安装”</p>
<p class="step"><a href="install.php" class="button">开始安装</a></p>
<?php
	else :
		$handle = fopen(ABSPATH . 'wp-config.php', 'w');
		foreach( $configFile as $line ) {
			fwrite($handle, $line);
		}
		fclose($handle);
		chmod(ABSPATH . 'wp-config.php', 0666);
		display_header();
?>
<p>恭喜！WordPress和数据库的连接已经建立。准备好了？ 开始 &hellip;</p>

<p class="step"><a href="install.php" class="button">开始安装</a></p>
<?php
	endif;
	break;
}
?>
</body>
</html>
