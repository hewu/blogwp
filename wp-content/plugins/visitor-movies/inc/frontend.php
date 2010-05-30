<?php
/**
 * Frontend rlass
 *
 * @package VisitorMoviesWordPress
 * @subpackage frontend
 * @since 0.1
 */

class VisitorMoviesWordPressFrontend extends VisitorMoviesWordPress {

	/**
	 * Load the log script when we are in the footer action?
	 *
	 * @since 0.1
	 * @var boolean
	 */
	var $footer_script_log = false;

	/**
	 * Load the playback script when we are in the footer action?
	 *
	 * @since 0.1
	 * @var boolean
	 */
	var $footer_script_playback = false;

	/**
	 * PHP 4 Style constructor which calls the below PHP5 Style Constructor
	 *
	 * @since 0.1
	 * @return none
	 */
	function VisitorMoviesWordPressFrontend() {
		$this->__construct();
	}

	/**
	 * Setup WordPress frontend 
	 *
	 * @return none
	 * @since 0.1
	 */
	function __construct () {
		VisitorMoviesWordPress::__construct ();

		if ( ! empty ( $this->options ) ) {
			if ( $this->get_option( 'enable' ) != '1' )
				return;

			wp_register_style( 'visitor-movies-playback', plugins_url('visitor-movies/css/visitor-movies.css') );
			wp_register_script( 'visitor-movies-playback', plugins_url('visitor-movies/js/visitor-movies-playback.js'), array('jquery'), '0.2.0.2', true );
			wp_register_script( 'visitor-movies-log', plugins_url('visitor-movies/js/visitor-movies-log.js'), array('jquery'), '0.2.0.2', true );

			if ( $_GET['playback'] == 'true' ) {
				$this->enable_playback();
			}
			else {
				if ( $this->get_option( 'enableeverywhere' ) == '1' )
					$this->enable_log();

			}
			// shortcode active during playback to hide it
			add_shortcode( 'visitor-movies', array( &$this, 'enable_log' ) );
		}
	}

	/**
	 * Do everything that's necessary so that loggin is active
	 *
	 * @return none
	 * @since 0.1
	 */
	 function enable_log() {
		if ( $_GET['playback'] !== 'true' ) { // don't load log script during playback...
			$percentage = $this->get_option( 'percentage' );
			if ( rand( 0, 100 ) <= $percentage ) {
				add_action( 'wp_footer', array( &$this, 'footer_scripts_log' ), 8 );
				add_action( 'wp_footer', array( &$this, 'footer_homelink' ), 9 );
			}
		}
	 }

	/**
	 * Do everything that's necessary so that playback is active
	 *
	 * @return none
	 * @since 0.2.2
	 */
	 function enable_playback() {
		global $current_user;
		global $VisitorMoviesFrontend;
		$current_user = new WP_User( null, null );
		add_action( 'wp_footer', array( &$VisitorMoviesFrontend, 'footer_infowin' ), 10 );
		add_action( 'wp_footer', array( &$this, 'footer_scripts_playback' ), 8 );
		wp_enqueue_style( 'visitor-movies-playback' );
		//wp_enqueue_script( 'jquery-ui-core' );
		// wp's version is too old...
		wp_enqueue_script( 'foobar', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js', array( 'jquery' ) );
	 }

	/**
	 * Add log scripts in the footer
	 *
	 * @return none
	 * @since 0.1
	 * @fixme don't run when user logged in
	 */
	function footer_scripts_log() {
		add_action( 'wp_footer', array( &$this, 'printglobal' ) );
		wp_print_scripts( 'visitor-movies-log' );
		wp_print_scripts( 'json2' );
	}

	/**
	 * Add playback scripts in the footer
	 *
	 * @return none
	 * @since 0.1
	 */
	function footer_scripts_playback() {
		add_action( 'wp_footer', array( &$this, 'printglobal_playback' ) );
		//add_action( 'wp_footer', array( &$this, 'playback_images' ) );
		wp_print_scripts( 'visitor-movies-playback' );
		wp_print_scripts( 'json2' );
	}

	/**
	 * Add a link to the plugin homepage in the footer
	 *
	 * @return none
	 * @since 0.1
	 */
	function footer_homelink() {
		if ( $this->get_option('homelink') === '1' ) { ?>
			<a href="http://www.nkuttler.de/wordpress/visitor-movies/">Website Visitor Movie</a> <?php
		}
	}

	/**
	 * Some global variables that are necessary
	 *
	 * @return none
	 * @since 0.1
	 * @todo: enqueue through ajax or something
	 */
	function printglobal() { ?>
		<script type="text/javascript">
		<!--
			var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
			var Ymd = "<?php echo date( 'Ymd', time() ) ?>";
			var His = "<?php echo date( 'His', time() ) ?>";
		//-->
		</script> <?php
	}
	
	/**
	 * Some global variables that are necessary for playback
	 *
	 * @return none
	 * @since 0.1
	 * @todo: enqueue through ajax or something
	 * @todo own class ?
	 */
	function printglobal_playback() { ?>
		<script type="text/javascript">
		<!--
			var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
			var Ymd = "<?php echo $_GET['Ymd'] ?>";
			var sha1 = "<?php echo $_GET['sha1'] ?>";
			var His = "<?php echo $_GET['His'] ?>";
		//-->
		</script> <?php
	}

}
