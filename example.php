<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>NOAA Weather Grabber &ndash; Sample</title>
</head>
<body>





<?php
// Require the weather function
require_once( 'weather.php' );

// Get the weather
$weather = noaa_weather_grabber( 'KMSP', 'yes' );

// Display a header with the weather location 
?>
<h1><?php echo $weather->location; ?> Weather</h1>
<?php

// Make sure the weather data is updated
if ((isset ( $weather->okay )) && ( $weather->okay == "yes" )) {

// Display the weather
?>
<img src="icons-medium/<?php echo $weather->imgCode; ?>.png" alt="<?php echo $weather->condition; ?>" width="86" height="86" />
<p><?php echo $weather->temp; ?> &deg;</p>
<p><?php echo $weather->condition; ?></p>
<?

}

// If weather is outdated, display an error
else {
?>
<p>Weather is not up to date.</p>
<?php	
}
?>





</body>
</html>