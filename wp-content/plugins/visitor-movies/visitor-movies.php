<?php
/*
	Copyright 2010 Nicolas Kuttler (email : wp@nicolaskuttler.de )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

Plugin Name: Visitor Movies
Author: Nicolas Kuttler
Author URI: http://www.wordpress-dienstleistungen.de/
Version: 0.3.1.2
*/

/**
 * @since 0.1
 * @package VisitorMovies
 * @subpackage pluginwrapper
 */
class VisitorMoviesWordPress {

	/**
	 * Array containing the options
	 *
	 * @since 0.1
	 * @var string
	 */
	var $options;

	/**
	 * PHP 4 Style constructor which calls the below PHP5 Style Constructor
	 *
	 * @since 0.2
	 * @return none
	 */
	function VisitorMoviesWordPress() {
		$this->__construct();
	}

	/**
	 * Load options
	 *
	 * @return none
	 * @since 0.1
	 */
	function __construct () {
		$this->options = get_option ( 'VisitorMovies' );
	} 

	/**
	 * Return a specific option value
	 *
	 * @param string $option name of option to return
	 * @return mixed 
	 * @since 0.1
	 */
	function get_option( $option ) {
		if ( isset ( $this->options[$option] ) )
			return $this->options[$option];
		else
			return false;
	}

	/**
	 * return plugin URL
	 *
	 * @return string
	 * @since 0.1
	 */
	function plugin_url () {
		return plugins_url ( plugin_basename ( dirname ( __FILE__ ) ) );
	}

}

/**
 * Instantiate the VisitorMoviesFrontend or VisitorMoviesAdmin Class
 */
if ( is_admin () ) {
	require_once ( dirname ( __FILE__ ) . '/inc/VisitorMoviesAdmin.php' );
	$VisitorMoviesAdmin = new VisitorMoviesAdmin();
	require_once ( dirname ( __FILE__ ) . '/inc/admin.php' );
	$VisitorMoviesWordPressAdmin = new VisitorMoviesWordPressAdmin();
} else {
	require_once ( dirname ( __FILE__ ) . '/inc/VisitorMoviesFrontend.php' );
	$VisitorMoviesFrontend = new VisitorMoviesFrontend();
	require_once ( dirname ( __FILE__ ) . '/inc/frontend.php' );
	$VisitorMoviesWordPressFrontend = new VisitorMoviesWordPressFrontend();
}
