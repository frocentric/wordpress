<?php


namespace NFMailchimp\EmailCRM\WpBridge\Contracts;

// phpcs:disable

/**
 * Contract for classes that wrap the WordPress database API `WPDB`
 */
interface WpdbInterface
{
    /**
     * Set a new instance of WPDB.
     *
     * @param  \WPDB $wpdb
     * @return $this
     */
    public function setWpdb(\WPDB $wpdb): WpdbInterface;

    /**
     * Get current instance of WPDB
     *
     * @return \WPDB
     */
    public function getWpdb(): \WPDB;

    /**
     * Sql to query, using WordPress
     *
     * wpdb::getResults($sql)
     *
     * @param  string $sql The sql query to get results for
     * @return \stdClass[]
     */
    public function getResults(string $sql);
}
// phpcs:enable

