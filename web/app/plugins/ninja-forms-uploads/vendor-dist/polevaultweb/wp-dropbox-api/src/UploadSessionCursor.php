<?php

namespace NF_FU_VENDOR\Polevaultweb\WPDropboxAPI;

class UploadSessionCursor
{
    /**
     * @var string
     */
    public $session_id;
    /**
     * @var int
     */
    public $offset;
    public function __construct($session_id, $offset = 0)
    {
        $this->session_id = $session_id;
        $this->offset = $offset;
    }
}
