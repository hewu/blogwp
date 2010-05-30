<?php
/**
 * Base Class for VisitorMovies
 * Only holds the path to the logs atm.
 *
 * @package VisitorMovies
 * @subpackage base
 * @since 0.2
 */
class VisitorMovies {
	/**
	 * String containing the path to the log directory
	 *
	 * @since 0.2
	 * @var string
	 */
	var $log_dir;

	/**
	 * String containing the url to the pics directory
	 *
	 * @since 0.2
	 * @var string
	 */
	var $pics_url;

	/**
	 * String containing the path to the log directory
	 *
	 * @since 0.2
	 * @var string
	 * @fixme config file?
	 */
	function __construct () {
		if ( defined( 'WP_PLUGIN_URL' ) ) { // we're using wordpress
			$options = get_option( 'VisitorMovies' );
			$dir = $options['log_dir'];
			if ( is_dir( $dir ) && is_writeable( $dir ) )
				$this->log_dir = $options['log_dir'];
			else
				$this->log_dir	= WP_CONTENT_DIR . '/visitor-movies-logs/';
			$this->pics_url = WP_PLUGIN_URL . '/visitor-movies/pic/';
			$this->play_anchor = '<img src="' . $this->pics_url . 'control_play.png' . '" alt="play" />';
		}
	}
}


