<?php
/**
 * Admin class
 *
 * @package VisitorMoviesWordPress
 * @subpackage admin
 * @since 0.1
 */
class VisitorMoviesWordPressAdmin extends VisitorMoviesWordPress {
	
	/**
	 * Version of the options format
	 *
	 * @since 0.1
	 * @var string
	 */
	var $version = '0.2.2';

	/**
	 * Path to the main plugin file
	 *
	 * @since 0.1
	 * @var string
	 */
	var $plugin_file;

	/**
	 * PHP 4 Style constructor which calls the below PHP5 Style Constructor
	 *
	 * @since 0.1
	 * @return none
	 */
	function VisitorMoviesWordPressAdmin() {
		$this->__construct();
	}

	/**
	 * Setup WordPress backend
	 *
	 * @return none
	 * @since 0.1
	 */
	function __construct () {
		VisitorMoviesWordPress::__construct ();

		$this->check_upgrade();

		// Full path to main file
		$this->plugin_file = dirname ( dirname ( __FILE__ ) ) . '/visitor-movies.php';

		// Load localizations if available
		load_plugin_textdomain ( 'visitor-movies' , false , 'visitor-movies/translations' );

		// Activation hook
		register_activation_hook ( $this->plugin_file , array ( &$this , 'init' ) );

		// Whitelist options
		add_action ( 'admin_init' , array ( &$this , 'register_settings' ) );

		// Activate the options page
		add_action ( 'admin_menu' , array ( &$this , 'add_page' ) ) ;

		// Enable ajax handlers
		global $VisitorMoviesAdmin;
		add_action( 'wp_ajax_visitor_movies_log_get', array( &$VisitorMoviesAdmin, 'log_get' ) );
		add_action( 'wp_ajax_nopriv_visitor_movies_log', array( &$VisitorMoviesAdmin, 'log' ) );
		wp_register_script( 'tablesorter', plugins_url('visitor-movies/js/jquery.tablesorter.min.js'), array('jquery'), '2.0.3', true );
	}

	/**
	 * Whitelist the VisitorMovies options
	 *
	 * @since 0.1
	 * @return none
	 */
	function register_settings () {
		register_setting( 'VisitorMovies_options' , 'VisitorMovies' );
	}

	/**
	 * Return plugin default config
	 *
	 * @since 0.1
	 * @return array
	 */
	function defaults () {
		global $VisitorMoviesAdmin;
		$defaults = array (
				'version'			=>	'0.2.2',
				'enable'			=>  '0',
				'homelink'			=>  '1',
				'enableeverywhere'	=>	'0',
				'percentage'		=>	'10',
				'log_dir'			=>	$VisitorMoviesAdmin->log_dir,
		);
		return $defaults;
	}

	/**
	 * Initialize the default options during plugin activation
	 *
	 * @return none
	 * @since 0.1
	 */
	function init() {
		if ( ! get_option ( 'VisitorMovies' ) )
			add_option ( 'VisitorMovies' , $this->defaults() );
		else
			$this->check_upgrade();
	}

	/**
	 * Check if we need to perform an upgrade
	 *
	 * @return none
	 * @since 0.1.0.0
	 */
	 function check_upgrade() {
		if ( version_compare ( $this->get_option( 'version' ), $this->version, '<' ) )
			$this->upgrade();
	 }

	/**
	 * Perform an upgrade
	 *
	 * @return none
	 * @since 0.1.0.0
	 */
	 function upgrade() {
		/*
	 	if ( version_compare( $this->get_option( 'version' ), '0.1.0.0', '<' ) ) {
			// If the plugin was already in use we assume the user added the
			// select field by hand and hide it.
			$newopts = $this->defaults();
			$this->options = array_merge( $newopts, $this->options );
			$this->options['removeselect'] = '1';
			$this->options['version'] = '0.1.0.0'; // upgrade version field...
			update_option( 'VisitorMovies', $this->options );
		}
		*/
	 }

	/**
	 * Reset the plugin config
	 *
	 * @return none
	 * @since 0.1
	 */
	 function restore_defaults() {
	 	$this->options = $this->defaults();
		update_option( 'VisitorMovies', $this->options );
	 }

	/**
	 * Add the options page
	 *
	 * @return none
	 * @since 0.1
	 */
	function add_page() {
		if ( current_user_can ( 'manage_options' ) && function_exists ( 'add_options_page' ) ) {
			$options_page = add_options_page ( __( 'Visitor Movies' , 'visitor-movies' ) , __( 'Visitor Movies' , 'visitor-movies' ) , 'manage_options' , 'VisitorMovies' , array ( &$this , 'admin_page' ) );
			add_action( 'admin_head-' . $options_page, array( &$this, 'css' ) );
			add_action( 'admin_print_scripts-' . $options_page, array( &$this, 'script' ) );
			add_filter( 'ozh_adminmenu_icon_VisitorMovies', array ( &$this , 'icon' ));
		}
	}

	/**
	 * Load admin CSS style
	 *
	 * @since 0.1
	 * @todo isn't there some admin enqueue style function?
	 */
	function css() { ?>
		<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL . '/visitor-movies/css/admin.css?v=0.2.2' ?>" type="text/css" media="all" /> <?php
	}

	/**
	 * Load admin scripts
	 *
	 * @return none
	 * @since 0.2.1
	 */
	function script() {
		wp_enqueue_script( 'tablesorter' );
	}

	/**
	 * Return admin menu icon
	 *
	 * @return string path to icon
	 * @since 0.1
	 */
	function icon() {
		$url = $this->plugin_url();
		$url .= '/pic/film.png';
		return $url;
	}

	/**
	 * Output the options page
	 *
	 * @return none
	 * @since 0.1
	 */
	function admin_page () { ?>
		<div id="nkuttler" class="wrap" >
			<h2><?php _e( 'Visitor Movies for WordPress', 'visitor-movies' ) ?></h2> <?php
			require_once( 'nkuttler.php' );
			nkuttler0_2_2_links( 'visitor-movies' ) ?>

			<p> <?php
				_e( 'Did you ever want to know what exactly your visitors are doing on your site? Watch them!', 'visitor-movies' ); ?>
			</p> <?php

			global $VisitorMoviesAdmin;
			$dir = $VisitorMoviesAdmin->log_dir;
			if ( !is_dir( $dir ) || !is_writable( $dir ) ) {
				if ( !mkdir( $dir ) ) { ?>
					<div class="error"> <?php
						printf(  __("<code>%s</code> does not exist or the plugin can't write to it. Please create it or make sure the webserver can write to it.", 'visitor-movies' ), $dir ); ?>
					</div> <?php
				}
			}

			$this->configure();
			$VisitorMoviesAdmin->browselogs(); ?>
		</div>

<script>
<!--
jQuery(document).ready(function($) { 
    // call the tablesorter plugin 
    $("#visitormoviesreport").tablesorter(); 
}); 
//--!>
</script>

		<?php
	}

	/**
	 * Output the options
	 *
	 * @return none
	 * @since 0.1 estd.
	 */
	function configure() {
		if ( $this->get_option( 'reset' ) === '1' )
			$this->restore_defaults(); ?>

		<h2>Configure...</h2>
    	<form method="post" action="options.php"> <?php
			settings_fields( 'VisitorMovies_options' ); ?>
			<input type="hidden" name="VisitorMovies[version]" value="<?php echo $this->get_option( 'version' ) ?>" />
			<table class="form-table form-table-clearnone" >

				<tr valign="top">
					<th scope="row"> <?php
						_e( 'Activate the plugin?', 'visitor-movies' )?>
					</th>
					<td>
						<input name="VisitorMovies[enable]" type="checkbox" value="1" <?php checked( '1', $this->options['enable'] ); ?> />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"> <?php
						_e( 'Enable the plugin on all pages?', 'visitor-movies' )?>
					</th>
					<td>
						<input name="VisitorMovies[enableeverywhere]" type="checkbox" value="1" <?php checked( '1', $this->options['enableeverywhere'] ); ?> />
						<span><?php _e( 'I recommend to use the shortcode [visitor-movies] instead.', 'visitor-movies' ) ?></span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"> <?php
						_e( 'Thank the author by including a link to the plugin page in your footer?', 'visitor-movies' )?>
					</th>
					<td>
						<input name="VisitorMovies[homelink]" type="checkbox" value="1" <?php checked( '1', $this->options['homelink'] ); ?> />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"> <?php
						_e( "Percentage of visitors to log", 'visitor-movies' )?>
					</th>
					<td> <?php
						$this->form_select( 'percentage', array( '100', '75', '50', '25', '10', '5', '1' ) ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"> <?php
						_e( 'Path to log directory?', 'visitor-movies' )?>
					</th>
					<td>
						<input name="VisitorMovies[log_dir]" type="text" size="60" value="<?php global $VisitorMoviesAdmin; echo $VisitorMoviesAdmin->log_dir ?>" />
						<span><?php _e( 'You should put this outside the webserver\'s root direcory.', 'visitor-movies' ); ?></span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"> <?php
						_e( "Reset the form?", 'visitor-movies' )?>
					</th>
					<td>
						<input name="VisitorMovies[reset]" type="checkbox" value="1" />
					</td>
				</tr>

			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    	    </p>
    	</form> <?php
	}

	/**
	 * Prints a <select> and the listed <option>s
	 *
	 * @since 0.2.2
	 * @param string $name Form input name
	 * @param array $choices List of options
	 * @return none
	 */
	function form_select( $name, $choices ) { ?>
		<select name="VisitorMovies[<?php echo $name; ?>]"><?php
			// FIXME $option or selected should be passed as a parameter
			$select = $this->get_option( 'percentage' );
			foreach ( $choices as $choice ) {
				if ( $choice == $select ) {
					echo "<option value=\"$choice\" selected>" . __( $choice, 'nktagcloud' ) . "</option>\n";
				}
				else {
					echo "<option value=\"$choice\" >" . __( $choice, 'nktagcloud' ) . "</option>\n";
				}
			} ?>
		</select> <?php
	}
}
