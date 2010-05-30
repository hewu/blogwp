<?php
require_once( 'VisitorMovies.php' );

/**
 * VisitorMovies admin class. Does not depend on WordPress
 *
 * @package VisitorMovies
 * @subpackage admin
 * @since 0.2
 */
class VisitorMoviesAdmin extends VisitorMovies {
	/**
	 * Setup admin
	 *
	 * @return none
	 * @since 0.2
	 */
	function __construct () {
		VisitorMovies::__construct ();
	}

	function mkdir( $dir ) {
		if ( !is_dir( $dir ) )
			if ( !mkdir( $dir ) )
				die( "Cannot create dir $dir" );
			//chmod( $dir, 0770 );
			//if ( !chmod( $dir, 0770 ) )
			//	die( "Cannot change permissions on dir $dir" );
	}

	/**
	 * Handle ajax logging
	 *
	 * @return none
	 * @since 0.1
	 */
	function log() {
		$thelog			= stripslashes( $_POST['thelog'] );
		$data			= json_decode( $thelog, true );
		$Ymd            = sprintf( "%08d", intval( $data['config']['Ymd'] ) );
		$His            = sprintf( "%06d", intval( $data['config']['His'] ) );
		$url			= $data['config']['url'];
		$remote_addr	= $_SERVER['REMOTE_ADDR'];

		if ( !isset( $thelog ) )
			die( 'missing $thelog' );
		if ( !isset( $data ) )
			die( 'missing $data' );
		if ( !isset( $Ymd ) )
			die( 'missing $Ymd' );
		if ( !isset( $His ) )
			die( 'missing $His' );
		if ( !isset( $url ) )
			die( 'missing $url' );
		if ( !isset( $remote_addr ) )
			die( 'missing $remote_addr' );

		$data['config']['ip']	= $remote_addr;

		//$browser      = $_POST['browser'];

		$sha1		= sha1( $remote_addr );
		$log_dir	= $this->log_dir;
		$Ymddir		= $log_dir . $Ymd;
		$sha1dir	= $Ymddir . '/' . $sha1;
		$Hisdir		= $sha1dir . '/' . $His;
	
		$this->mkdir( $log_dir ); // @todo hm, remove?
		$this->mkdir( $Ymddir );
		$this->mkdir( $sha1dir );
		$this->mkdir( $Hisdir );
	
		$logfile = $Hisdir . '/log.txt';

		// add user info when creating the logfile
		//if ( !file_exists( $logfile ) )
		//	$thelog = "$php_self:::{$remote_addr}:::{$browser}{$thelog}";
		if ( file_exists( $logfile ) )
			unset( $data['config']);

		$fh = fopen( $logfile, 'a' ) or die( "Can't write file $logfile" );
		fwrite( $fh, serialize( $data ) );
		fwrite( $fh, "\n" );
		fclose( $fh );
		//chmod( $logfile, 0750 );
		die( strlen( $thelog ) . " bytes recorded in $logfile" );
	}
	
	/**
	 * Handle ajax get data requests
	 *
	 * @return none
	 * @since 0.1
	 */
	function log_get( ) {
		$dir	= $this->log_dir;
		$Ymd	= $_GET['Ymd'];
		$His	= $_GET['His'];
		$sha1	= $_GET['sha1'];

		$logfile= $dir . $Ymd . '/' . $sha1 . '/' . $His . '/log.txt';

		/* @todo write maintenance function */
		$lines = file( $logfile );
		$config = array();
		$events = array();
		foreach( $lines as $line ) {
			$data = unserialize( $line );
			if ( is_array( $data['events'] ) ) // @fixme why do we need this anyway?!
				$events = array_merge( $events, $data['events'] );
			// it's possible to add config later
			// @todo think about potential problems
			if ( isset( $data['config'] ) )
				$config = array_merge( $config, $data['config'] );
		}
		$data = new stdClass();
		$data->events = $events;
		$data->config = $config;
		$data = json_encode( $data );
		die( $data );
	}

	/**
	 * Show directory contents
	 *
	 * @return none
	 * @since 0.1
	 */
	function browselogs() {
		$dir = $this->log_dir;
		printf( '<h2>%s</h2>', __('Browsing logs...', 'visitor-movies' ) );

		// build link to current page... thanks wordpress...
		$selfurl = '?';
		foreach ( $_GET as $key => $value ) {
			$selfurl .= "$key=$value&amp;";
		}

		$Ymd = $_GET['Ymd'];

		if ( isset( $Ymd ) ) {
			$this->dailyreport( $Ymd  );
		}
		else { // todo function
			$daydirs = array();
			if ( is_dir( $dir ) ) {
				if ( $dh = opendir( $dir ) ) {
					while ( ( $file = readdir( $dh ) ) !== false ) {
						if ( $file === '.' || $file === '..' )
							continue;
						$daydirs[] = $file;
					}
				}
				else {
					echo "Can't open directory $dir";
				}
			}
			else {
				echo "$dir is not a directory";
			}
			sort( $daydirs );
			foreach ( $daydirs as $daydir ) {
				echo "<a href=\"{$selfurl}Ymd=$daydir\">$daydir</a> ";
			}
		}
	}

	/**
	 * Show a daily report
	 *
	 * @since 0.1
	 * @return none
	 */
	function dailyreport( $Ymd ) {
		$dir	= $this->log_dir . $Ymd;
		$data	= $this->fetchdata( $Ymd );

		/**
		 * Sort data alphabetically 
		 *
		 * @since 0.1
		 * @return array Whatever we were given, sorted
		 * @todo fixme...
		 */
		function compare_alphabet( $a, $b ) {
			return strnatcmp( $a['His'], $b['His'] );
		}
		usort( $data, 'compare_alphabet' );

		$this->printtable( $data, $Ymd );
	}

	/**
	 * Print table with preview links
	 *
	 * @since 0.1
	 * @return none
	 */
	function printtable( $data, $Ymd ) {
		if ( !is_array( $data ) ) {
			echo '<strong>No Data!</strong>';
			return;
		}

		// @fixme make all dynamic
		echo <<<EOF
		<table id="visitormoviesreport" >
			<thead>
				<tr>
					<th>Time</th>
					<th>URL</th>
					<th>referrer</th>
					<th>IP</th>
					<th>Browser</th>
					<th>Duration (seconds)</th>
					<th>Events</th>
					<th>Form</th>
					<th>Window Size</th>
					<th>Playback</th>
				</tr>
			</thead>
		<tbody>
EOF;
		foreach( $data as $row ) {
			echo '<tr>';
			foreach( $row as $cell => $value ) {
				if ( isset( $value ) )
					echo "<td>$value</td>";
				else
					echo "<td>fixme</td>";
			}
			echo '</tr>';
		}
		echo <<<EOF
			</tbody>
		</table>
EOF;
	}

	/**
	 * Read data for a day into an array
	 *
	 * @since 0.1
	 * @return array daily log summary
	 * @fixme this is kind of messy and has some duplication...
	 */
	function fetchdata( $Ymd ) {
		$Ymddir = $this->log_dir . $Ymd;

		if ( is_dir( $Ymddir ) ) {
			if ( $dh_sha1 = opendir( $Ymddir ) ) {

				while ( ( $sha1 = readdir( $dh_sha1 ) ) !== false ) {
					if ( $sha1 === '.' || $sha1 === '..' )
						continue;

					$sha1dir = $Ymddir . '/' . $sha1;

					// walk through all His dirs
					if ( is_dir( $sha1dir ) ) {
						if ( $dh_His = opendir( $sha1dir ) ) {
							while ( ( $Hisdir = readdir( $dh_His ) ) !== false ) {
								if ( $Hisdir === '.' || $Hisdir === '..' )
									continue;

								$logfile = $sha1dir . '/' . $Hisdir . '/log.txt';
								if ( !file_exists( $logfile ) )
									die ("$logfile doesn't exist");

								/* @todo write maintenance function */
								$lines = file( $logfile );
								$config = array();
								$events = array();
								foreach( $lines as $line ) {
									$data = unserialize( $line );
									if ( is_array( $data['events'] ) ) // @fixme why do we need this anyway?!
										$events = array_merge( $events, $data['events'] );
									// it's possible to add config later
									// @todo think about potential problems
									if ( isset( $data['config'] ) )
										$config = array_merge( $config, $data['config'] );
								}

								/* @todo function extract relevant info */
								$His		= $config['His'];
								$URI		= "<a href=" . $config['url'] . ">" . substr( preg_replace ( '/.*?(\/\/)/', '', $config['url'] ), 0, 40 ) . "</a>";
								$remote_ip	= $config['ip'];
								$referrer	= "<a href=" . $config['referrer'] . ">" . substr( preg_replace ( '/.*?(\/\/)/', '', $config['referrer'] ), 0, 20 ) . "</a>";
							
								$browser = 'unknown';
								foreach ( $config['browser'] as $key => $value ) {
									if ( $key == 'version' ) {
										$version = $value;
									}
									if( $value === true ) {
										$browser = $key;
									}
								}
								$browser .= " $version";
							
								$counter	= 0;
								$form		= 0;
								$resolution	= 'fixme';
								foreach ( $events as $event ) {
									$counter++;
									if ( $event['eventType'] == 'cb' || $event['eventType'] == 'dn' )
										$form++;
									elseif ( $event['eventType'] == 'x' ) {
										$width	= $event['data']['width'];
										$height	= $event['data']['height'];
										$resolution = "{$width}x{$height}";
									}
								}
							
								$last	= end( $events );
								$length = round( $last['timestamp'] / 1000 );

								$sha1 = sha1( $remote_ip );

								$parts = parse_url( $config['url'] );
								$playurl = $parts['scheme'] . '://' . $parts['host'] . $parts['path'] . "?playback=true&amp;Ymd={$Ymd}&amp;His={$His}&amp;sha1={$sha1}";
								if ( isset( $parts['fragment'] ) )
									$playurl .= "#" . $parts['fragment'];

								$play	= '<a href="' . $playurl . '" target="_blank">' . $this->play_anchor . '</a>';
								$play	.= '<a href="#" onclick="window.open(\'' . $playurl . '\', \'_blank\', \'menubar=1,toolbar=1,status=1,resizable=1,width=' . $width . ',height=' . $height . '\');" title="Open link in popup" >' . $this->play_anchor . '</a>';
							
								$return[] = array(
									'His'		=> $His,
									'URI'		=> $URI,
									'referrer'	=> $referrer,
									'IP'		=> $remote_ip,
									'browser'	=> $browser,
									'length'	=> $length,
									'counter'	=> $counter,
									'form'		=> $form,
									'resolution'=> $resolution,
									'play'		=> $play,
								);

							}
						}
						closedir( $dh_His );
					}
					else
						echo "not a directory: $sha1dir";
				}
			}
			closedir( $dh_sha1 );
		}
		else
			echo "not a directory: $Ymddir";

		return $return;
	}

}
