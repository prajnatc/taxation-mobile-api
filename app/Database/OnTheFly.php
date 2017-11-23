<?php

namespace App\Database;

use Config;
use DB;

class OnTheFly {

	/**
	 * The name of the database we're connecting to on the fly.
	 *
	 * @var string $database
	 */
	protected $database;

	/**
	 * The on the fly database connection.
	 *
	 * @var \Illuminate\Database\Connection
	 */
	protected $connection;

	/**
	 * Create a new on the fly database connection.
	 *
	 * @param  array $options
	 * @return void
	 */
	public function __construct($client_details)
	{
		// Set the database

        $clientConfiguration = array(
	        'driver'    => 'mysql',
	        'host'      => $client_details->client_hostname,
	        'database'  => $client_details->client_database,
	        'username'  => $client_details->client_username,
	        'password'  => $client_details->client_password,
	        'charset'   => 'utf8',
	        'collation' => 'utf8_unicode_ci',
	        'prefix'    => '',
	        'strict'    => false
	    );


        Config::set('database.connections.clientDB',$clientConfiguration);
				//DB::purge('clientDB');
		// Create the connection
		return $this->connection = DB::reconnect('clientDB');
	}

	/**
	 * Get the on the fly connection.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Get a table from the on the fly connection.
	 *
	 * @var    string $table
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function getTable($table = null)
	{
		return $this->getConnection()->table($table);
	}
}
