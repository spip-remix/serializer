<?php

declare(strict_types=1);

namespace SpipRemix\Component\Serializer;

use Spip\Component\Filesystem\FilesystemInterface;
use SpipRemix\Component\Serializer\Exception\EncoderException;
use SpipRemix\Component\Serializer\FreshInterface;
use SpipRemix\Contracts\EncoderInterface;

/**
 * Gestion du fichier de cache sécurisé des métas.
 *
 * @api
 *
 * @template T
 *
 * @author JamesRezo <james@rezo.net>
 */
class SecuredFileSerializer implements EncoderInterface, FreshInterface
{
    public const PHP_SECURED_HEADER = '<?php die (\'Acces interdit\'); ?>' . "\n";

    /**
     * @param non-empty-string $filename Chemin absolu du fichier sécurisé
     */
    public function __construct(
        private FilesystemInterface $filesystem,
        private EncoderInterface $serializer,
        private string $filename,
    ) {
    }

    /**
     * @param T $decoded
     */
    public function encode(mixed $decoded): string
    {
        $encoded = $this->serializer->encode($decoded);
        $encoded = self::PHP_SECURED_HEADER . $encoded;

        if (!\is_dir(dirname($this->filename))) {
            $this->filesystem->mkdir(\dirname($this->filename));
        }

        if (!$this->filesystem->write($this->filename, $encoded)) {
            EncoderException::throw('d\'ecriture');
        }

        return $encoded;
    }

    /**
     * @return T
     */
    public function decode(string $encoded): mixed
    {
        $decoded = null;

        $encoded = $this->filesystem->read($this->filename);
        if (!empty($encoded)) {
            $encoded = substr($encoded, strlen(self::PHP_SECURED_HEADER));
            $decoded = $this->serializer->decode($encoded);
            if ($decoded === false) {
                throw EncoderException::throw('erreur');
            }
        }

        return $decoded;
    }

    public function fresh(int $now, int $ttl): mixed
    {
        $timestamp = $this->filesystem->exists($this->filename)
            ? $this->filesystem->mtime($this->filename) : 0 ;

        if ($now - $timestamp < $ttl) {
            return $this->decode('');
        }

        $this->filesystem->remove($this->filename);

        return null;
    }

    public function refresh(mixed $data): bool
    {
        $this->encode($data);

        return true;
    }
}
