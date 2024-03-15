<?php

declare(strict_types=1);

namespace SpipRemix\Component\Serializer;

use SpipRemix\Contracts\EncoderInterface;
use SpipRemix\Component\Serializer\Exception\EncoderException;

/**
 * Sérialisation/Dé-sérialisation Native de PHP.
 *
 * @api
 *
 * @template T of null|bool|int|float|string|array|object
 *
 * @author JamesRezo <james@rezo.net>
 */
class NativeSerializer implements EncoderInterface
{
    /**
     * @param T $decoded
     */
    public function encode(mixed $decoded): string
    {
        // Éviter les fonctions anonymes
        if ($decoded instanceof \Closure) {
            EncoderException::throw('fonction');
        }

        // Ne sérialiser que les objets et les tableaux
        if (\is_array($decoded) || \is_object($decoded)) {
            return \serialize($decoded);
        }

        /** @var string $decoded */
        return $decoded;
    }

    /**
     * @return T
     */
    public function decode(string $encoded): mixed
    {
        // Éviter un warning PHP
        \set_error_handler(function (int $errno, ...$unused) {
            if ($errno == 2) {
                return true;
            }

            return false;
        }, \E_WARNING);
        $decoded = \unserialize($encoded);
        \restore_error_handler();

        // Si la chaîne encodée n'est pas sérialisable,
        // c'est une chaîne stockée directement en base.
        if ($decoded === false) {
            /** @var T $encoded */
            return $encoded;
        }

        /** @var T $decoded */
        return $decoded;
    }
}
