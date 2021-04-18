<?php

namespace NF_FU_VENDOR\Aws\ClientSideMonitoring\Exception;

use NF_FU_VENDOR\Aws\HasMonitoringEventsTrait;
use NF_FU_VENDOR\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for client-side monitoring.
 */
class ConfigurationException extends \RuntimeException implements \NF_FU_VENDOR\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
