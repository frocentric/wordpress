<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NF_FU_VENDOR\Monolog\Handler;

use NF_FU_VENDOR\Monolog\Logger;
use NF_FU_VENDOR\Monolog\Formatter\NormalizerFormatter;
use NF_FU_VENDOR\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends \NF_FU_VENDOR\Monolog\Handler\AbstractProcessingHandler
{
    private $client;
    public function __construct(\NF_FU_VENDOR\Doctrine\CouchDB\CouchDBClient $client, $level = \NF_FU_VENDOR\Monolog\Logger::DEBUG, $bubble = \true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        $this->client->postDocument($record['formatted']);
    }
    protected function getDefaultFormatter()
    {
        return new \NF_FU_VENDOR\Monolog\Formatter\NormalizerFormatter();
    }
}
