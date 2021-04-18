<?php

namespace NF_FU_VENDOR\Aws\S3\UseArnRegion\Exception;

use NF_FU_VENDOR\Aws\HasMonitoringEventsTrait;
use NF_FU_VENDOR\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for S3's UseArnRegion
 */
class ConfigurationException extends \RuntimeException implements \NF_FU_VENDOR\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
