<?php
/**
 * Current NOAA Weather Grabber
 * version 3.0.0
 *
 * This lightweight PHP script gets the current weather condition,
 * temperature, and the name of a corresponding condition image
 * from NOAA and makes the data available for use in a PHP document.
 * It includes a built-in JSON cache.
 *
 * See readme.md for more information.
 *
 * Web URL: https://github.com/TomLany/Weather-Grabber
 * Modified heavily by: Tom Lany, http://tomlany.net/
 * Based on: https://github.com/UCF/Weather-Data
 *

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


/**
 * Configuration
 * Set these variables to match your desired configuration.
 * More information is available in readme.md.
 **/

// Enter the full file path to your cache data folder. Make sure the folder is writable.
define( 'CACHEDATA_FILE_PATH', '' );

// Enter your timezone code from http://php.net/manual/en/timezones.php. This is set to America/Chicago by default.
define( 'TIMEZONE', 'America/Chicago' );

// Enter the cache duration, in seconds.
define( 'WEATHER_CACHE_DURATION', 1800 );

// End of configuration -- you're done!
// See readme.md for more information about including this in your script.


/**
 * Functions
 * This area sets up some functions used throughout the script.
 */

// Defines the URL that the weather will be grabbed from
function noaa_weather_grabber_weather_url( $city ) {
	$weather_url = 'http://w1.weather.gov/xml/current_obs/' . $city . '.xml';
	return $weather_url;
}

// Defines the file that will be saved on the server
function noaa_weather_grabber_cache_file( $city ) {
	$cachedata_file = CACHEDATA_FILE_PATH.'weather_data_' . $city . '.json';
	return $cachedata_file;
}

// End this function if the weather feed cannot be found
function noaa_weather_grabber_check_feed( $weather_url ) {
	$weather_url_headers = get_headers( $weather_url );
	if ( $weather_url_headers[0] == "HTTP/1.1 200 OK" ) {
		return TRUE;
	}
	elseif ( $weather_url_headers[0] == "HTTP/1.0 200 OK" ) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

// Set a timeout and grab the weather feed
function noaa_weather_grabber_get_feed( $weather_url ) {
	$opts = array( 	'http' => array(
					'method' => 'GET',
					'timeout' => 5		// seconds
					));
	$context = stream_context_create( $opts );
	$raw_weather = file_get_contents( $weather_url, false, $context );
	if ( $raw_weather ) {
		return simplexml_load_string( $raw_weather );
	}
	else {
		return FALSE;
	}
}

// Save the cached data to file
function noaa_weather_grabber_write_to_file( $weather, $cachedata_file ) {
	// JSON encode data for caching
	$weather_encoded = json_encode( $weather );

	// Write the new string of data to the cache file
	$filehandle = fopen( $cachedata_file , 'w' ) or die( 'Cache file write failed.' );
	fwrite( $filehandle, $weather_encoded );
	fclose( $filehandle );
}

// Set the timezone
date_default_timezone_set( TIMEZONE );


/**
 * Caching function
 * Returns an array of weather data and saves the data
 * to a cache file for later use.
 **/
function noaa_weather_grabber_make_new_cachedata( $city, $use_cache ) {

	// Define variables
	$weather_url = noaa_weather_grabber_weather_url( $city );
	$cachedata_file = noaa_weather_grabber_cache_file( $city );
	$continue = "yes";

	// Check if the feed is working
	$feed_check = noaa_weather_grabber_check_feed( $weather_url );
	if ( $feed_check == FALSE ) {
		$continue = "no";
	}

	// Get the feed
	if ( $continue == "yes" ) {
		$raw_weather = noaa_weather_grabber_get_feed( $weather_url );
		if ( $raw_weather == FALSE ) {
			$continue = "no";
		}
	}

	// Sanatize the weather information and add it to a variable
	if ( $continue == "yes" ) {
		$imgCodeNoExtension = explode( '.png', $raw_weather->icon_url_name );

		$weather = new stdClass();
		$weather->okay			= "yes";
		$weather->location		= htmlentities( $raw_weather->location, ENT_QUOTES );
		$weather->condition		= htmlentities( $raw_weather->weather, ENT_QUOTES );
		$weather->temp			= htmlentities( number_format (( integer ) $raw_weather->temp_f )); // strip decimal place and following
		$weather->imgCode		= htmlentities( $imgCodeNoExtension[0], ENT_QUOTES );
		$weather->feedUpdatedAt	= htmlentities( $raw_weather->observation_time_rfc822, ENT_QUOTES );
		$weather->feedCachedAt	= date( 'r' );

		// Write the weather to file
		if ( $use_cache !== "no" ) {
			noaa_weather_grabber_write_to_file( $weather, $cachedata_file );
		}

		// Return the newly grabbed content
		return( $weather );

	}

	// If there was an error, produce error message
	if ( $continue == "no" ) {
		$weather = new stdClass();
		$weather->okay = "no";
		return( $weather );
	}

}


/**
 * Main function
 * Returns either previously cached data or newly fetched data
 * depending on whether or not it exists and whether or not the
 * cache time has expired.
 **/
function noaa_weather_grabber( $city = NULL, $use_cache = "yes" ) {

	// Make sure $city is capitalized
	$city = strtoupper( $city );

	// Get cache file location
	$cachedata_file = noaa_weather_grabber_cache_file( $city );

	// Set continue variable
	$continue = "yes";

	// See if cached data is available and usable
	if (( $use_cache == "no" ) || ( $use_cache == "update" )) {$continue = "cacheOff";}
	if ( $city == NULL ) {$continue = "cityError";}
	if ( $continue == "yes" ) {
		if (( file_exists( $cachedata_file ) ) && ( date('YmdHis', filemtime( $cachedata_file )) > date( 'YmdHis', strtotime( 'Now -'.WEATHER_CACHE_DURATION.' seconds' )))) {}
		else {$continue = "outdated";}
	}

	// Provide the cached data or get new data
	if ( $continue == "yes" ) {
		$raw_weather = file_get_contents( $cachedata_file ) or die( 'Cache file open failed.' );
		$raw_weather = json_decode( $raw_weather );
		if ( $raw_weather->okay == "yes" ) {
			// Sanitize weather in a new variable
			$weather = new stdClass();
			$weather->okay			= htmlentities( $raw_weather->okay, ENT_QUOTES );
			$weather->location		= htmlentities( $raw_weather->location, ENT_QUOTES );
			$weather->condition		= htmlentities( $raw_weather->condition, ENT_QUOTES );
			$weather->temp			= htmlentities( number_format (( integer ) $raw_weather->temp ));
			$weather->imgCode		= htmlentities( $raw_weather->imgCode, ENT_QUOTES );
			$weather->feedUpdatedAt	= htmlentities( $raw_weather->feedUpdatedAt, ENT_QUOTES );
			$weather->feedCachedAt	= htmlentities( $raw_weather->feedCachedAt, ENT_QUOTES );

			return( $weather );
		}
		else {
			return noaa_weather_grabber_make_new_cachedata( $city, $use_cache );
		}
	}
	elseif ( $continue == "cityError" ) {
		$weather = new stdClass();
		$weather->okay = "no";
		return( $weather );
	}
	else {
		return noaa_weather_grabber_make_new_cachedata( $city, $use_cache );
	}

}


?>