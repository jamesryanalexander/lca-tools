<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2016-02-10

Quick and Dirty tool to download feedback from 2016 Strategy Consultation

---------------------------------------------   */

$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
mb_internal_encoding( 'UTF-8' );

$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db ); // set up mysql connection
$mysql->set_charset( "utf8" );

if ( $mysql->connect_error ) {
	echo 'Database connection fail: '  . $mysql->connect_error, E_USER_ERROR;
}

$gettype = ( !empty( $_GET['data'] ) ) ? $_GET['data'] : ""; // grab type of data to send

if ( $gettype === 'metadata' ) {
	$select = 'SELECT user,country,homewiki,homeregistration,globaledits,metaedits,metaregistration FROM strategycomments_2016';
	$headers = array('user','country','homewiki', 'homeregistration','globaledits','metaedits','metaregistration');
} elseif ( $gettype === 'comments' ) {
	$select = 'SELECT user,commentsreach,commentscommunities,commentsknowledge FROM strategycomments_2016';
	$headers = array('user','commentsreach','commentscommunities','commentsknowledge');
} elseif ( $gettype === 'all' ) {
	$select = 'SELECT user,country,homewiki,homeregistration,globaledits,metaedits,metaregistration,commentsreach,commentscommunities,commentsknowledge FROM strategycomments_2016';
	$headers = array('user','country','homewiki','homeregistration','globaledits','metaedits','metaregistration','commentsreach','commentscommunities','commentsknowledge');
} else {
	$gettype = '';
}

if ( $gettype != '' ) {
	$data = $mysql->query( $select );
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=strategydata.csv');
$output = fopen('php://output', 'w');

if ( $gettype != '' ) {

	fputcsv( $output, $headers );

	while ( $row = $data->fetch_assoc() ) {

		fputcsv( $output, $row );

	}

} else {
	echo 'No Data Found';
}

fclose( $output );


?>