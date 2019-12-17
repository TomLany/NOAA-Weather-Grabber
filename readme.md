# NOAA Weather Grabber
version 4.0.4

This lightweight PHP script gets the current weather condition, temperature, and the name of a corresponding condition image from the  National Oceanic and Atmospheric Administration (NOAA)'s National Weather Service (NWS) and makes the data available for use in your PHP script/website.

A built-in caching mechanism saves the results to a JSON file. Requests made within the cache period receive cached data. The cache is updated during the first request after it expires.

Requires PHP 5.1.0 or later.

Notes:
* As of version 4.0.0, this script utilizes NOAA's new weather API. Read the changelog below for more about the functional changes that were made in version 4.0.0 as a result of the new API. Changes to the script you use to read the data may be needed when updating.
* NOAA began requiring that weather scripts specify a user agent in the request header. Versions of this script prior to 3.1.0 no longer work. Upgrading to version 3.1.0 or later is recommended.
* This script provides weather information from NOAA, which covers the United States only.

Website:
* Web URL: [https://github.com/TomLany/NOAA-Weather-Grabber](https://github.com/TomLany/NOAA-Weather-Grabber)
* Modified heavily and expanded by: Tom Lany, [http://tomlany.net/](http://tomlany.net/)
* Based on: [https://github.com/UCF/Weather-Data](https://github.com/UCF/Weather-Data)

Copyright:

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

To use this script, you will need to edit the configuration, include the script, and call the function. Learn more below:

## Setup

### Save the File
You need to save the `weather.php` file on the web server where you will be using it.

### Set the Configuration
A few configuration variables at the top of `weather.php` need to be modified to make this work with your setup. What to enter for each variable:

#### `CACHEDATA_FILE_PATH`
Enter the full file path to the location where you want this script to save its data, including the trailing slash. Make sure the script has access to this location (the directory is writable). It's a good idea to save the data outside of the web tree, so that other people can't view the cached data directly on the web. For example, if your username is bubba and you want to store data in a folder on your server called weather, you might type `/home/bubba/weather/`.

#### `WEBSITE_URL`
Enter the URL of the website you will be using this script on. This information will be sent to NOAA as a part of the user agent request header when grabbing weather data. NOAA blocks requests without a user agent header set, and recommends providing this information. Any URL format is acceptable.

#### `EMAIL_ADDRESS`
Enter your email address. This information will be sent to NOAA as a part of the user agent request header when grabbing weather data. NOAA blocks requests without a user agent header set, and recommends providing this information so they can contact you if there are any problems with your use of their data that might result in them blocking your use of the service.

#### `WEATHER_CACHE_DURATION`
Specify how often you want the weather data cache updated in seconds. By default, the script requests data once every hour (every 3600 seconds). NOAA currently refreshes their feed data once each hour at about fifty minutes after each hour.

By not updating the cache constantly, your website will remain fast and you will request data from NOAA at a reasonable frequency. It is possible that NOAA could block your access to weather data if you request data at an unreasonable rate. Because NOAA's data is only updated once each hour (just before the top of the hour), new data is only available at this frequency. The cache needs to refresh frequently enough so the weather data is current, though. Note the cache only updates when pages where this script is included are loaded.

#### `SCRIPT_VERSION`
This is the version of the script that you are using. Leave this value as it is set.

## Include the Weather on Your Website

Once you have filled in the configuration information, you are about ready to pull weather data! A full example of these steps (include the file, call the function, and get the data - great for testing) is included in `sample.php`. An example that pulls the most commonly accessed data with some basic HTML markup (that can be copied into a website - great as a working starting place) is included in `example.php`.

### Include the File
When you're ready to use the plugin, include it in the script you would like to present weather information in. Specify the path to the `weather.php` file on your server.

`require_once( 'weather.php' );`

### Call the Function
To use NOAA Weather Grabber, call this function:

`$weather = noaa_weather_grabber( 'KMSP', 'yes' );`

The function has two arguments that you can set where you call it:

#### `$stationID` (Required)
The first argument lets you specify the four-letter code for the location that you want to use. Go to [weather.gov](http://www.weather.gov/) and search for the location you want. On the resulting page, just above the current temperature, you will find "Current conditions at:", followed by the monitoring location and its four letter code in parenthesis. You should enter this four letter code here. For example, if you want weather data from Central Park in Washington, D.C., type `KDCA`. The location you enter must be four digits. If the location you find is not four letters, you need to find another nearby location with a four-letter code to use. Try larger nearby cities.

#### `$use_cache` (Optional, defaults to `yes`)
In the second argument, you can specify if you want to use the cache. It is strongly recommended that you use the cache, as it will speed page loads, and make responsible use of the external data source.

* To use the cache, specify `"yes"`.
* To NOT use the cache, specify `"no"`. 
* To use the cache, but force the cache to be updated each time the page is loaded, specify `"update"`.

### Get the Data
The weather data is returned from the function as an array. Use the data most relevant to your project. The following are the keys the in the array and a description of the values they contain:

* `okay` - Tells whether or not the function ran and gathered weather data. "yes" is returned if the function was successful, otherwise "no" is returned.
* `location` - Reports the weather location using the four-letter city code.
* `condition` - This gives the current weather condition in words, such as "Sunny".
* `temp` - The current temperature is displayed in Fahrenheit. You may want to include the degree sign (`&deg;` in HTML), and/or an F following the temperature.
* `imgCode` - A weather image code, consisting of day or night, then a "/", then the image name, without the file extension. Images that NOAA uses are included in this package in PNG format. The same images are included in all three folders. The images in the `icons-large` folder are 134px x 134px. The images in the `icons-medium` folder are 86px x 86px. The images in the `icons-small` folder are 56px x 56px. 
* `feedUpdatedAt` - This indicates the time NOAA's feed says the weather information was updated, in [ISO8601 format](http://www.php.net/manual/en/class.datetime.php). The UTC timezone is used.
* `feedCachedAt` - This indicates the time the weather information was cached on your server, in [ISO8601 format](http://www.php.net/manual/en/class.datetime.php). The UTC timezone is used.

A full example of how to display this data is included in `example.php`.

## Additional Notes

### Check the Cache
Once this script is working with the file you want to include weather data in, make sure the cache is working. Find the cache file on your server and ensure that it is not updated every time you update the page the weather is included on, but only as often as the cache is run. It is important that the cache is running properly to ensure good performance.

## Changelog

### 4.0.4
* Added `example.php` as an example of how to use this tool (with code that can be copied into a webpage).

### 4.0.3
* Minor update to use `/stations/{stationId}/observations/latest` API endpoint, as previous endpoint was deprecated.
* Require quality checked data from NOAA.

### 4.0.2
* Don't get weather data if the cache is on, the currently saved file indicates there was an error getting data and the file was saved less than 15 minutes ago.
* Don't get weather data when the data being provided is over 1.5 hours old.

### 4.0.1
* A small update to revise the way the data is gathered from NOAA (this is now required for the script to function).
* Improve temperature rounding.

### 4.0.0
* This script now utilizes [NOAA's new weather API](https://forecast-v3.weather.gov/documentation) for data. The point forecast weather grabbing method has been removed. The main weather function call no longer includes a third argument, which was previously used for the point forecast option.
* `location` now returns the four letter city code, as opposed to the name of the location.
* `imgCode` format changed so that the text provided first states day or night, then a "/", then the image names NOAA is using with the API. Previously, just the image name was provided. The images provided with the script have been updated to follow this format, which does not match the previous format.
* `feedUpdatedAt` and `feedCachedAt` now use [ISO8601 format](http://www.php.net/manual/en/class.datetime.php). They are returned in UTC timezone, as opposed the user-specified timezone provided previously. The configuration constant for specifying a timezone has been removed.

### 3.1.0
* Script now sends a unique HTTP request header, to conform with a new NOAA requirement that HTTP request headers be sent.
* Adds additional configuration fields for a website URL, email address and script version to comply with new NOAA request header requirements. Be sure to check your configuration at the top of the file.
* Adds the ability to get point forecast data.
* The default cache duration is now 3600 seconds.

A changelog was not available before version 3.1.0.

## Questions?
If you have any questions or issues, feel free to [leave a comment in the issue tracker](https://github.com/TomLany/NOAA-Weather-Grabber/issues).
