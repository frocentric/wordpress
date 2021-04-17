<?php

namespace NF_FU_VENDOR;

// Don't redefine the functions if included multiple times.
if (!\function_exists('NF_FU_VENDOR\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
