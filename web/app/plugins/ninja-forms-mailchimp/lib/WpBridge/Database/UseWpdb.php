<?php


namespace NFMailchimp\EmailCRM\WpBridge\Database;

use NFMailchimp\EmailCRM\WpBridge\Contracts\WpdbInterface;

class UseWpdb implements WpdbInterface
{

	/**
	 * @var \wpdb
	 */
	protected $wpdb;
	public function __construct(\wpdb $wpdb)
	{
		$this->setWpdb($wpdb);
	}

	/**
	 * @inheritDoc
	 */
	public function setWpdb(\wpdb $wpdb): WpdbInterface
	{
		$this->wpdb = $wpdb;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getWpdb(): \wpdb
	{
		return  $this->wpdb;
	}

	/**
	 * @inheritDoc
	 */
	public function getResults(string $sql)
	{
		return $this->wpdb->get_results($sql);
	}
}
