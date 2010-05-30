<?php
require_once( 'VisitorMovies.php' );

/**
 * VisitorMovies frontend class. Does not depend on WordPress
 *
 * @package VisitorMovies
 * @subpackage frontend
 * @since 0.2
 */
class VisitorMoviesFrontend extends VisitorMovies {

	/**
	 * Add infowin in the footer
	 *
	 * @return none
	 * @since 0.1
	 */
	function footer_infowin() {
		$click  = $this->pics_url . 'click.png';
		$cursor = $this->pics_url . 'cursor.png'; ?>

		<div id="visitor-movies-frontend-container">
			<img id="click" src="<?php echo $click ?>" alt="click" />
			<img id="cursor" src="<?php echo $cursor ?>" alt="cursor" />
	
			<div id="visitor-movies-flashwin">loading...</div>
			<div id="visitor-movies-actionwin">loading...</div>
			<div id="visitor-movies-infowin">
				<div id="visitor-movies-time-total">loading...</div>
				<div id="visitor-movies-time-elapsed">loading...</div>
			</div>
			<div id="visitor-movies-thanks">
				<div id="visitor-movies-thanks-close">
					<a href="#" onclick="jQuery('#visitor-movies-thanks').fadeOut(1000); return false">close</a>
				</div>
				<h1>Thank you for using <a href="http://www.nkuttler.de/2010/05/21/record-movies-of-visitors/">Visitor Movies</a></h1>
				<p>
					<a href="http://www.nkuttler.de/2010/05/21/record-movies-of-visitors/"><strong>Visitor Movies</strong></a> is free software written by <a href="http://www.nkuttler.de">Nicolas Kuttler</a>.
					I hope that you enjoy using it and that it helps you to improve your website.
				</p>
				<p>
					If you are interested in contributing to this project, do not hesitate to <a href="http://www.nkuttler.de/contact/">contact me</a>.
					Code contributions, feature suggestions and <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-">donations</a> are welcome.
				</p>
				<p>
					If you need a professional coder do not hesitate to contact me.
					Skills include:
					<ul>
						<li>valid (X)HTML</li>
						<li>CSS</li>
						<li>JavaScript, jQuery, mootools</li>
						<li>PHP</li>
						<li>SEO</li>
						<li>Perl</li>
						<li>WordPress</li>
						<li>Typo3</li>
						<li>SQL</li>
						<li>Apache</li>
						<li>Linux/Unix</li>
					</ul>
				</p>
			</div>
		</div> <?php
	}

}
