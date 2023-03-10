<?php
/**
 * © Copyright (C) 2013 - 2023 <Marius Schröder>. Born 1999
 * All rights reserved.
 *
 * @author      Marius Schröder
 * @copyright   Copyright (c) 2013 - 2023, Marius Schröder
 * @link        https://schroeder.systems/
 * @mail        copyright@schroeder.systems
 *
 * Please include the full copyright for the name, domain and
 * email address if you use any part of the code in your imprint
 * or publicly readable on your website
 */

if(!empty($_REQUEST['hash']))
{
	header('Content-Type: application/json; charset=utf-8');
	try
	{
		$data = json_decode($_REQUEST['formdata'], true, 512, JSON_THROW_ON_ERROR);
	}
	catch(JsonException $e)
	{
		$notice = [
			'error_message' => $e->getMessage(),
			'error_file'    => $e->getFile(),
			'line'          => $e->getLine(),
			'trace'         => $e->getTrace(),
		];

		echo json_encode($notice, JSON_PRETTY_PRINT);
		exit;
	}

	$content = [];
	foreach($data as $key => $value)
	{
		// quick and dirty way  to get contents from frontend (not my idea)
		preg_match_all('/(.*?)-(.*?)$/m', $key, $matches, PREG_SET_ORDER, 0);

		$matches = $matches[0];

		if(!empty($matches[1]))
		{
			// map $matches to fit the layout of the rteu.json file
			$content[$matches[1]][$matches[2]] = $value;
		}
	}

	// save file with new (changed) content
	$saveFile = file_put_contents(__DIR__.'/rteu.json', json_encode($content, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

	if($saveFile)
	{
		$notice = [
			'message' => 'Successfully inserted data',
			'return'  => true,
			'notice'  => $content,
		];
	}
	else
	{
		$notice = [
			'message' => 'Failed to inserted data',
			'return'  => false,
			'notice'  => $content,
		];
	}

	echo json_encode($notice, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
	exit;
}
else
{
	http_response_code(404);
}
