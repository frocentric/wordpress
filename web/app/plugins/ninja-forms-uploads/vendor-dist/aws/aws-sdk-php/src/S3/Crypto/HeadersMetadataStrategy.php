<?php

namespace NF_FU_VENDOR\Aws\S3\Crypto;

use NF_FU_VENDOR\Aws\Crypto\MetadataStrategyInterface;
use NF_FU_VENDOR\Aws\Crypto\MetadataEnvelope;
class HeadersMetadataStrategy implements \NF_FU_VENDOR\Aws\Crypto\MetadataStrategyInterface
{
    /**
     * Places the information in the MetadataEnvelope in to the Meatadata for
     * the PutObject request of the encrypted object.
     *
     * @param MetadataEnvelope $envelope Encryption data to save according to
     *                                   the strategy.
     * @param array $args Arguments for PutObject that can be manipulated to
     *                    store strategy related information.
     *
     * @return array Updated arguments for PutObject.
     */
    public function save(\NF_FU_VENDOR\Aws\Crypto\MetadataEnvelope $envelope, array $args)
    {
        foreach ($envelope as $header => $value) {
            $args['Metadata'][$header] = $value;
        }
        return $args;
    }
    /**
     * Generates a MetadataEnvelope according to the Metadata headers from the
     * GetObject result.
     *
     * @param array $args Arguments from Command and Result that contains
     *                    S3 Object information, relevant headers, and command
     *                    configuration.
     *
     * @return MetadataEnvelope
     */
    public function load(array $args)
    {
        $envelope = new \NF_FU_VENDOR\Aws\Crypto\MetadataEnvelope();
        $constantValues = \NF_FU_VENDOR\Aws\Crypto\MetadataEnvelope::getConstantValues();
        foreach ($constantValues as $constant) {
            if (!empty($args['Metadata'][$constant])) {
                $envelope[$constant] = $args['Metadata'][$constant];
            }
        }
        return $envelope;
    }
}
