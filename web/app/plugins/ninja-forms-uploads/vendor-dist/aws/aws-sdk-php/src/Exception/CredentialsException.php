<?php

namespace NF_FU_VENDOR\Aws\Exception;

use NF_FU_VENDOR\Aws\HasMonitoringEventsTrait;
use NF_FU_VENDOR\Aws\MonitoringEventsInterface;
class CredentialsException extends \RuntimeException implements \NF_FU_VENDOR\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
