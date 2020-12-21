<?php
/*
 * PHP EpSolar Tracer Class (PhpEpsolarTracer) v0.9
 *
 * Library for communicating with 
 * Epsolar/Epever Tracer BN MPPT Solar Charger Controller
 *
 * THIS PROGRAM COMES WITH ABSOLUTELY NO WARRANTIES !
 * USE IT AT YOUR OWN RISKS !
 *
 * Copyright (C) 2016 under GPL v. 2 license
 * 13 March 2016
 *
 * @author Luca Soltoggio
 * http://www.arduinoelettronica.com/
 * https://arduinoelectronics.wordpress.com/
 *
 * This is an example on how to use the library
 *
 * It queries and prints all charger controller's registries
 *
 * lsusb
 * Bus 001 Device 006: ID 04e2:1411 Exar Corp. 
 * 
 * ls /dev/tty*
 * /dev/ttyACM0 <-- this is no bueno
 * /dev/ttyXRUSB0 <-- should be
 * 

 * https://github.com/toggio/PhpEpsolarTracer/issues/4
 * 
 * 
 * get Exar USB Serial Driver driver files from: https://github.com/kasbert/epsolar-tracer/tree/master/xr_usb_serial_common-1a

 * sudo apt-get install dkms raspberrypi-kernel-headers  
 * 	sudo cp -a ../xr_usb_serial_common-1a /usr/src/
 *	dkms add -m xr_usb_serial_common -v 1a
 *  dkms build -m xr_usb_serial_common -v 1a
 *  dkms install -m xr_usb_serial_common -v 1a
 * 
	Tips for Debugging
	------------------
	* Check that the USB UART is detected by the system
		# lsusb
	* Check that the CDC-ACM driver was not installed for the Exar USB UART
		# ls /dev/tty*

		To remove the CDC-ACM driver and install the driver:

		# rmmod cdc-acm
		# modprobe -r usbserial
		# modprobe usbserial
		# insmod ./xr_usb_serial_common.ko

 *  sudo chmod 666 /dev/ttyUSB0 <- permissions
 */
 
require_once 'PhpEpsolarTracer.php';

function post_to_firebase(){

	$timestamp = date("c");
	$local_time = new DateTime("now", new DateTimeZone('America/Chicago'));

	$data = '{"timestamp": "' . $timestamp . '", "local_time": "' . $local_time->format('Y-m-d H:i:s')  . '", "voltage": "44.3v"}';

	$url = "https://cabin-3bebb.firebaseio.com/solar_stats.json";

	// use key 'http' even if you send the request to https://...
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => $data,
			'ignore_errors' => true
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { 
		/* Handle error */ 
		print $result;
	}
	
	var_dump($result);
}

$tracer = new PhpEpsolarTracer('/dev/ttyXRUSB0');


if ($tracer->getInfoData()) {
	print "Info Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->infoData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->infoKey[$i].": ".$tracer->infoData[$i]."\n";
	} else print "Cannot get Info Data\n";

if ($tracer->getRatedData()) {
	print "Rated Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->ratedData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->ratedKey[$i].": ".$tracer->ratedData[$i].$tracer->ratedSym[$i]."\n";
	} else print "Cannot get Rated Data\n";

if ($tracer->getRealtimeData()) {
	print "\nRealTime Data\n";
	print "----------------------------------\n";

	echo implode(" ",$tracer->realtimeData);
	for ($i = 0; $i < count($tracer->realtimeData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->realtimeKey[$i].": ".$tracer->realtimeData[$i].$tracer->realtimeSym[$i]."\n";
		#post_to_firebase(44);
	} else print "Cannot get RealTime Data\n";

if ($tracer->getStatData()) {
	print "\nStatistical Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->statData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->statKey[$i].": ".$tracer->statData[$i].$tracer->statSym[$i]."\n";
	} else print "Cannot get Statistical Data\n";
	
if ($tracer->getSettingData()) {
	print "\nSettings Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->settingData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->settingKey[$i].": ".$tracer->settingData[$i].$tracer->settingSym[$i]."\n";
	} else print "Cannot get Settings Data\n";

if ($tracer->getCoilData()) {
	print "\nCoils Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->coilData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->coilKey[$i].": ".$tracer->coilData[$i]."\n";
	} else print "Cannot get Coil Data\n";

if ($tracer->getDiscreteData()) {
	print "\nDiscrete Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->discreteData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->discreteKey[$i].": ".$tracer->discreteData[$i]."\n";
	} else print "Cannot get Discrete Data\n";
?>