<?php


namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Shared\Traits\ConvertsFromArrayWithSnakeCaseing;
use NFMailchimp\EmailCRM\Shared\Traits\ConvertsToArrayWithSnakeCaseing;

abstract class NFBridgeEntity extends SimpleEntity
{
	use ConvertsFromArrayWithSnakeCaseing,ConvertsToArrayWithSnakeCaseing;
}
