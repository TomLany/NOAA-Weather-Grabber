# Current NOAA Weather Grabber
version 3.1.0

This lightweight PHP script gets the current weather condition, temperature, and the name of a corresponding condition image from NOAA and makes the data available for use in your PHP script/website.

A built-in caching mechanism saves the results to a JSON file. Requests made within the cache period receive cached data. The cache is updated during the first request after it expires.

Requires PHP 5.1.0 or later.

Notes:
* NOAA began requiring that weather scripts specify a user agent in the request header. Versions of this script prior to 3.1.0 no longer work. Upgrading to version 3.1.0 or later is recommended.
* The function name and way you access the data have changed in version 3.0.0. If you are upgrading from an older version, you may need to modify the way you call the function, as the old method no longer works. Please read the readme, below, for more information on how to use the current function.

* Web URL: [https://github.com/TomLany/Weather-Grabber](https://github.com/TomLany/Weather-Grabber)
* Modified heavily and expanded by: Tom Lany, [http://tomlany.net/](http://tomlany.net/)
* Based on: [https://github.com/UCF/Weather-Data](https://github.com/UCF/Weather-Data)

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

To use this script, you will need to edit the configuration, include the script, and call the function. Learn more below:

## Setup

### Save the File
You need to save the `weather.php` file on the web server where you will be using it.

### Set the Configuration
A couple of configuration variables at the top need to be modified to make this work with your setup. See the top of `weather.php`. What to enter for each variable:

#### `CACHEDATA_FILE_PATH`
Enter the full file path to the location where you want this script to save its data, including the trailing slash. Make sure the script has access to this location (the directory is writable). It's a good idea to save the data outside of the web tree, so that other people can't view the cached data directly on the web. For example, if your username is bubba and you want to store data in a folder on your server called weather, you might type `/home/bubba/weather/`.

#### `WEBSITE_URL`
Enter the URL of the website you will be using this script on. This information will be sent to NOAA as a part of the user agent request header when grabbing weather data. NOAA blocks requests without a user agent header set, and recommends providing this information.

#### `EMAIL_ADDRESS`
Enter your email address. This information will be sent to NOAA as a part of the user agent request header when grabbing weather data. NOAA blocks requests without a user agent header set, and recommends providing this information so they can contact you if there are any problems with your use of their data that might result in them blocking your use of the service.

#### `TIMEZONE`
Enter your timezone in PHP format. This is set to America/Chicago by default. You can see the list of possible timezones by going to [PHP's List of Supported Timezones](http://php.net/manual/en/timezones.php) or by running `DateTimeZone::listIdentifiers();` in PHP 5.2 or later. For example, if you want to use the New York City, NY, USA timezone, enter `America/New_York`.

#### `WEATHER_CACHE_DURATION`
Specify how often you want the weather data cache updated in seconds. By default, the script requests data once every half hour (every 1800 seconds). NOAA currently refreshes their feed data at about fifty minutes after each hour.

By not updating the cache constantly, your website will remain fast and you will request data from NOAA at a reasonable frequency. It is possible that NOAA could block your access to weather data if you request data at an unreasonable rate. The cache needs to refresh frequently enough so the weather data is current, though. Note the cache only updates when pages where this script is included are loaded.

#### `SCRIPT_VERSION`
This is the version of the script that you are using. Leave this value as it is set.

## Include the Weather on Your Website

Once you have filled in the configuration information, you are about ready to pull weather data! A full example of these steps (include the file, call the function, and get the data) is also included in `sample.php`.

### Include the File
When you're ready to use the plugin, include it in the script you would like to present weather information in. Specify the path to the `weather.php` file on your server.

`require_once( 'weather.php' );`

### Call the Function
To use NOAA Weather Grabber, call this function:

`$weather = noaa_weather_grabber( 'KMSP', 'yes', NULL );`

The function has three arguments that you can set where you call it:

#### `$city` (Required)
The first argument lets you specify the 4-letter code for the location that you want to use. Go to [weather.gov](http://www.weather.gov/) and search for the location you want. On the resulting page, [currently] just above the current temperature, you will find "Current conditions at:", followed by the monitoring location and its four letter code in parenthesis. You should enter this four letter code here. For example, if you want weather data from Central Park in Washington, D.C., type `KDCA`. If the location you are viewing has a code that is not four digits, you can still enter it here, but you must enter in a point forecast URL in the third argument.

#### `$use_cache` (Optional, defaults to `yes`)
In the second argument, you can specify if you want to use the cache. It is strongly recommended that you use the cache, as it will speed page loads, and make responsible use of the external data source.

* To use the cache, specify `"yes"`.
* To NOT use the cache, specify `"no"`. 
* To use the cache, but force the cache to be updated each time the page is loaded, specify `"update"`.

#### `$point_forecast_url` (Optional)
In the third argument, you can optionally specify a point forecast feed URL to use. By default, the script will pull data from [NOAA's Current Weather Condition XML Feeds](http://w1.weather.gov/xml/current_obs/). If you specify a point forecast URL here, the script will instead pull the data from this address. To find a point forecast URL, first go to [weather.gov](http://www.weather.gov/) and search for the location you want. Then, scroll down and find the orange `XML` button. Follow the link and copy the URL for this feed into this argument. By using this method, you can get weather from locations without four letter airport codes.

### Get the Data
The weather data is returned from the function as an array. Use the data most relevant to your project. The following are the keys the in the array and a description of the values they contain:

* `okay` - Tells whether or not the function ran and gathered weather data. "yes" is returned if the function was successful, otherwise "no" is returned.
* `location` - Reports the weather location.
* `condition` - This gives the current weather condition in words, such as "Sunny".
* `temp` - The current temperature is displayed in Fahrenheit. You may want to include the degree sign (`&deg;` in HTML), and/or an F following the temperature. If you'd prefer to use Celsius, you could change `temp_f` to `temp_c` in `weather.php`.
* `imgCode` - A weather image code, without the file extension, is outputted. Weather images from NOAA are included in this package. The [2015 NOAA icon set is utilized](http://www.weather.gov/forecast-icons). An [older icon set is also available on NOAA's website](http://w1.weather.gov/xml/current_obs/weather.php). The images in the `icons-large` folder are 136px x 136px. The images in the `icons-large` folder are 86px x 86px. The same images are included in both folders. Additional copies of some images were made so the image set is backwards compatible with older filenames for the icons.
* `feedUpdatedAt` - This indicates the time NOAA's feed says the weather information was updated, in [RFC2822 format](http://www.php.net/manual/en/class.datetime.php#datetime.constants.rfc2822).
* `feedCachedAt` - This indicates the time the weather information was cached on your server, in [RFC2822 format](http://www.php.net/manual/en/class.datetime.php#datetime.constants.rfc2822).

An example of how to display this data is included in `sample.php`.

## Additional Notes

### Check the Cache
Once this script is working with the file you want to include weather data in, make sure the cache is working. Find the cache file on your server and ensure that it is not updated every time you update the page the weather is included on, but only as often as the cache is run. It is important that the cache is running properly to ensure good performance.

## Changelog
Since 3.1.0

### 3.1.0
* Script now sends a unique HTTP request header, to conform with a new NOAA requirement that HTTP request headers be sent.
* Adds additional configuration fields for a website URL, email address and script version to comply with new NOAA request header requirements. Be sure to check your configuration at the top of the file.
* Adds the ability to get point forecast data.
* The default cache duration is now 3600 seconds.

## Questions?
If you have any questions or issues, feel free to [leave a comment in the issue tracker](https://github.com/TomLany/Weather-Grabber/issues).