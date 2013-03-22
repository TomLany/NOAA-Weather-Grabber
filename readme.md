# Current NOAA Weather Grabber

This lightweight PHP script gets the current weather condition, temperature, and the name of a corresponding condition image from NOAA and makes the data available for use in a PHP document.

A built-in caching mechanism saves the results to a JSON file. Requests made within the cache period receive cached data. The cache is updated for the first request after it expires.

This was designed to handle current weather conditions for a single location. It could be expanded to handle full forecasts and/or multiple locations.

* Web URL: [https://github.com/TomLany/Weather-Grabber](https://github.com/TomLany/Weather-Grabber)
* Modified heavily by: Tom Lany, [http://tomlany.net/](http://tomlany.net/)
* Based on: [https://github.com/UCF/Weather-Data](https://github.com/UCF/Weather-Data)

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

## Setup
You need to save the `weather.php` file on your web server where it can be used by the file you want to include weather data on.

Next, you'll need change just a few configuration variables to make it work with your setup. Find these variables at the top of the weather.php file.

### `WEATHER_CITY_CODE`
Specify the 4-letter code for the location that you want to use. Go to [weather.gov](http://www.weather.gov/) and search for the location you want. On the resulting page, [currently] to the right of the current temperature, you will find "Current conditions at:", followed by the monitoring location and it's four letter code in parenthesis. You should enter this four letter code into `WEATHER_CITY_CODE`. For example, if you want weather data from Central Park in New York City, NY, USA, type `KNYC`.

### `CACHEDATA_FILE_PATH`
Enter the full file path to the location where you want this script to save it's data, including the trailing slash. Make sure the script has access to this location (the directory is writable). It's a good idea to save the data outside of the web tree, so that other people can't view the cached data directly on the web. For example, if your username is bubba and you want to store data in a folder on your server called weather, you might type `/home/bubba/weather/`.

### `TIMEZONE`
Enter your timezone in PHP format. This is set to America/Chicago by default. You can see the list of possible timezones by going to [PHP's List of Supported Timezones](http://php.net/manual/en/timezones.php) or by running `DateTimeZone::listIdentifiers();` in PHP 5.2 or later. For example, if you want to use the New York City, NY, USA timezone, enter `America/New_York`.

### `WEATHER_CACHE_DURATION`
Specify how often you want the weather data cache updated in seconds. By default, the script requests data once per hour (every 3600 seconds). NOAA currently refreshes their feed data about fifty minutes after each hour.

Refraining from updating too frequently will keep your website fast and request data from NOAA less. The cache needs to refresh frequently enough so the weather data is current, though. This script may not update at the same time each hour; it only updates as data is requested.

### `WEATHER_URL` and `CACHEDATA_FILE`
These variables store the full path to the weather data location and cache data file, respectively. You probably won't need to edit them.

## Using the Data
Once you have filled in the configuration information, you are about ready to pull weather data!

### Include `weather.php`
You will need to include the `weather.php` file in the page where you want weather data by typing the following:

	<?php require_once('weather.php'); ?>

Make sure the require location corresponds to the location of the `weather.php` file on your server.

### Use the variables
You're ready to use the data! The weather data is included in four variables. You can echo each one where you want your weather data. You don't have to use each variable, and you can certainly omit some if that suits your project best. Each variable is described in more detail below.

#### `$weather_condition;`
This indicates the current weather condition, such as "Sunny".

#### `$weather_temp;`
This indicates the current temperature in Fahrenheit, such as "80". You may want to include the degree sign (which is `&deg;` in HTML), and/or an F following the temperature.

If you'd prefer to use Celsius, you could change `temp_f` to `temp_c` in weather.php on approximately line 111.

#### `$weather_imgCode;`
This script outputs a weather image code, without a file extension. Weather images are not included here. The images NOAA uses (which have corresponding file names) [are located here on their website](http://w1.weather.gov/xml/current_obs/weather.php).

#### `$weather_feedUpdatedAt;`
This indicates the time NOAA's feed says the weather information was updated. Note this is NOT the time the data was cached locally (which is likely to be a more recent time).

You may wish to put the HTML you use surrounding the weather data in conditional tags so that it isn't used if no weather data is returned. Here's an example of how you can do that:

	<?php
		if ($weather_condition !== "") {
			echo '<p>'.$weather_condition.'</p>';
		}
	?>

### Check the cache
Once this script is working with the file you want to include weather data in, make sure the cache is working. Find the cache file on your server and ensure that it is not updated every time you update the page the weather is included on, but only as often as the cache is run. It is imperative that the cache is run properly to ensure good performance.

## Notes
* If the remote weather data file cannot be found, or if there is a problem with it, the an empty cache file will be saved. This blank file will cause the script to try to find the weather data and cache it next time the script is run. If you are having problems getting this script going, make sure the remote file location in `CACHEDATA_FILE` looks correct.

## Examples
This is a simple example of what you do to pull weather information in a PHP file. Don't forget to include the correct path to your `weather.php` file.

	<?php
	require_once('weather.php');
	
	echo $weather_condition;
	echo $weather_temp;
	echo $weather_imgCode;
	echo $weather_feedUpdatedAt;
	
	?>

The following example includes conditional tags and sample HTML you could use to display weather data in a PHP file. Don't forget to include the correct path to your image library and your `weather.php` file.

	<?php require_once('weather.php'); ?>
	
	<ul>
	
		<?php if ($weather_imgCode !== "") { ?>
			<li><img src="<?php echo 'img/'.$weather_imgCode.'.jpg' ?>" alt="<?php echo $weather_condition; ?>" /></li>
		<?php } ?>
	
		<?php if ($weather_temp !== "") { ?>
			<li><?php echo $weather_temp; ?>&deg; F</li>
		<?php } ?>
	
		<?php if ($weather_condition !== "") { ?>
			<li><?php echo $weather_condition; ?></li>
		<?php } ?>
	
	</ul>

## Questions?
If you have any questions/issues, feel free to [leave a comment in the issue tracker](https://github.com/TomLany/Weather-Grabber/issues).