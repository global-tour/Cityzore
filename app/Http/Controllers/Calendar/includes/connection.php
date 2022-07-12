<?php

	// DB Connection Configuration
	define('DB_HOST', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '5k?-t)-Wz^PU.ua[');
	define('DATABASE', 'cityzore_g');
	define('TABLE', 'calendar');

	// Path to the files for upload
	define('SITE_FILES_URL', 'https://www.cityzore.com/calendar-2.2.16/files/');

	// Default Categories
	$categories = array("General", "Off Day");

	/*
	true - will make all events on the database public and visible to anyone.
	false - will make events private and visible to the respective owner only.
	*/
	define('PUBLIC_PRIVATE_EVENTS', true);

	// Feature to import events
	define('IMPORT_EVENTS', true);

?>
