<?php

namespace NF_FU_VENDOR\Aws\Arn;

use NF_FU_VENDOR\Aws\Arn\S3\AccessPointArn as S3AccessPointArn;
use NF_FU_VENDOR\Aws\Arn\S3\BucketArn;
/**
 * @internal
 */
class ArnParser
{
    /**
     * @param $string
     * @return bool
     */
    public static function isArn($string)
    {
        return \strpos($string, 'arn:') === 0;
    }
    /**
     * Parses a string and returns an instance of ArnInterface. Returns a
     * specific type of Arn object if it has a specific class representation
     * or a generic Arn object if not.
     *
     * @param $string
     * @return ArnInterface
     */
    public static function parse($string)
    {
        $data = \NF_FU_VENDOR\Aws\Arn\Arn::parse($string);
        if (\substr($data['resource'], 0, 11) === 'accesspoint') {
            if ($data['service'] === 's3') {
                return new \NF_FU_VENDOR\Aws\Arn\S3\AccessPointArn($string);
            }
            return new \NF_FU_VENDOR\Aws\Arn\AccessPointArn($string);
        }
        return new \NF_FU_VENDOR\Aws\Arn\Arn($data);
    }
}
