<?php

// Based on the version created by Wikimedia

define( 'MW_NO_SESSION', 1 );

require_once '/srv/mediawiki/config/initialise/MirahezeFunctions.php';
require MirahezeFunctions::getMediaWiki( 'includes/WebStart.php' );

use MediaWiki\MediaWikiServices;

function streamFavicon() {
	global $wgFavicon;
	wfResetOutputBuffers();

	$favicon = $wgFavicon;
	if ( $favicon === '/favicon.ico' ) {
		$favicon = '/favicons/default.ico';
	}

	$req = RequestContext::getMain()->getRequest();
	if ( $req->getHeader( 'X-Favicon-Loop' ) !== false ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		return;
	}

	$url = wfExpandUrl( $favicon, PROTO_CANONICAL );
	$client = MediaWikiServices::getInstance()
		->getHttpRequestFactory()
		->create( $url );
	$client->setHeader( 'X-Favicon-Loop', '1' );

	$status = $client->execute();
	if ( !$status->isOK() ) {
		$favicon = '/favicons/default.ico';

		$url = wfExpandUrl( $favicon, PROTO_CANONICAL );
		$client = MediaWikiServices::getInstance()
			->getHttpRequestFactory()
			->create( $url );

		$status = $client->execute();
		if ( !$status->isOK() ) {
			header( 'HTTP/1.1 500 Internal Server Error' );
			return;
		}
	}

	$content = $client->getContent();
	header( 'Content-Length: ' . strlen( $content ) );
	header( 'Content-Type: ' . $client->getResponseHeader( 'Content-Type' ) );
	header( 'Cache-Control: public' );
	header( 'Expires: ' . gmdate( 'r', time() + 86400 ) );
	echo $content;
}

streamFavicon();
