<?php
/**
 * Current NOAA Weather Grabber
 *
 * This lightweight PHP script gets the current weather condition,
 * temperature, and the name of a corresponding condition image 
 * from NOAA and makes the data available for use in a PHP document
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
 *
 **/

// Enter the four letter NOAA city code from http://weather.gov/.
define('WEATHER_CITY_CODE', '');

// Enter the full file path to your cache data folder. Make sure the folder is writable.
define('CACHEDATA_FILE_PATH', '');

// Enter your timezone code from http://php.net/manual/en/timezones.php.
define('TIMEZONE', '');

// Enter the cache duration, in seconds.
define('WEATHER_CACHE_DURATION', 3600);

// You probably won't need to touch these.
define('WEATHER_URL', 'http://w1.weather.gov/xml/current_obs/'.WEATHER_CITY_CODE.'.xml');
define('CACHEDATA_FILE', CACHEDATA_FILE_PATH.'weather_data_'.WEATHER_CITY_CODE.'.json');

// End of configuration -- you're done!
// See readme.md for more information about including this in your script.


/**
 * Returns either previously cached data or newly fetched data
 * depending on whether or not it exists and whether or not the
 * cache time has expired.
 *
 * @return array
 **/
function get_weather_data() {

	date_default_timezone_set(TIMEZONE);

	// Check if cached weather data already exists
	if (file_exists(CACHEDATA_FILE)) {
		$weather_string = file_get_contents(CACHEDATA_FILE) or die('Cache file open failed.');

		// The cache time must be within now and the cache duration
		// to return cached data:
		if ($weather_string) {

			// Checks to see if the cache needs to be updated
			if ( date('YmdHis', filemtime(CACHEDATA_FILE)) > date('YmdHis', strtotime('Now -'.WEATHER_CACHE_DURATION.' seconds')) ) {
				$weather = (json_decode($weather_string, true));
				return $weather;
			}
			else {
				return make_new_cachedata();
			}

		}
		else {
			return make_new_cachedata();
		}

	}
	else {
		return make_new_cachedata();
	}

}


/**
 * Returns an array of weather data and saves the data
 * to a cache file for later use.
 *
 * @return array
 **/
function make_new_cachedata() {

	// End this function if the weather feed cannot be found
	$weather_url_headers = get_headers(WEATHER_URL);
	if ($weather_url_headers[0] !== "HTTP/1.0 200 OK") {
		return;
	}

	// Setup an empty array for weather data
	$weather = array(
		'condition'			=> '',
		'temp'				=> '',
		'imgCode'			=> '',
		'feedUpdatedAt'		=> '',
	);

	// Set a timeout and grab the weather feed
	$opts = array('http' => array(
							'method' => 'GET',
							'timeout' => 5		//seconds
							));
	$context = stream_context_create($opts);
	$raw_weather = file_get_contents(WEATHER_URL, false, $context);

	// If the weather feed can be fetched, grab and sanatize the needed information
	if ($raw_weather) {
		$xml = simplexml_load_string($raw_weather);

		$weather['condition'] 		= htmlentities((string)$xml->weather);
		$weather['temp']			= htmlentities(number_format((string)$xml->temp_f)); // strip decimal place
		$weather['imgCode']			= htmlentities((string)$xml->icon_url_name);
		$weather['feedUpdatedAt'] 	= htmlentities((string)$xml->observation_time_rfc822);
	}

	// Setup a new string for caching
	$weather_string = json_encode($weather);

	// Write the new string of data to the cache file
	$filehandle = fopen(CACHEDATA_FILE, 'w') or die('Cache file open failed.');
	fwrite($filehandle, $weather_string);
	fclose($filehandle);

	// Return the newly grabbed content
	return $weather;
}


/**
 * Grab, sanatize, and put the data in variables.
 *
 **/
$weather_array = get_weather_data();

$weather_condition 		= htmlentities($weather_array['condition']);
$weather_temp 			= htmlentities($weather_array['temp']);
$weather_imgCode 		= htmlentities($weather_array['imgCode']);
$weather_feedUpdatedAt 	= htmlentities($weather_array['feedUpdatedAt']);

?>