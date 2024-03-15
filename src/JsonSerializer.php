<?php

declare(strict_types=1);

namespace SpipRemix\Component\Serializer;

use SpipRemix\Contracts\EncoderInterface;
use SpipRemix\Component\Serializer\Exception\EncoderException;

/**
 * Sérialisation/Dé-sérialisation JSON.
 *
 * @api
 *
 * @template T of object|array<float|int|string>
 *
 * @author JamesRezo <james@rezo.net>
 */
class JsonSerializer implements EncoderInterface
{
    /**
     * @param T $decoded
     */
    public function encode(mixed $decoded): string
    {
        // Éviter les fonctions anonymes
        if ($decoded instanceof \Closure) {
            EncoderException::throw(...['fonction']);
        }

        $encoded = \json_encode($decoded);
        if ($encoded === false) {
            EncoderException::throw(...[$decoded]);
        }

        return $encoded;
    }

    /**
     * @return T
     */
    public function decode(string $encoded): mixed
    {
        return \json_decode($encoded);
    }
}
