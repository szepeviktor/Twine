<?php

namespace PHLAK\Twine\Traits;

use PHLAK\Twine\Exceptions\EncryptionException;
use PHLAK\Twine\Exceptions\DecryptionException;

trait Encryptable
{
    /** @var array Supported cipher methods */
    protected $supportedCiphers = [
        'aes-128-cbc',
        'AES-128-CBC',
        'aes-256-cbc',
        'AES-256-CBC'
    ];

    /**
     * Encrypt the string.
     *
     * @param string $key    The key for encrypting
     * @param string $cipher The cipher method
     *
     * Supported cipher methods:
     *
     *   - AES-128-CBC (default)
     *   - AES-256-CBC
     *
     * @throws \PHLAK\Twine\Exceptions\EncryptionException
     *
     * @return self
     */
    public function encrypt(string $key, string $cipher = 'AES-128-CBC') : self
    {
        if (! in_array($cipher, $this->supportedCiphers)) {
            throw new EncryptionException('The cipher must be one of: ' . implode(', ', $this->supportedCiphers));
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $ciphertext = openssl_encrypt($this->string, $cipher, $key = md5($key), 0, $iv);

        $json = json_encode([
            'iv' => $iv = base64_encode($iv),
            'ciphertext' => $ciphertext,
            'hmac' => hash_hmac('sha256', $iv . $ciphertext, $key)
        ]);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptionException('Failed to encrypt the string');
        }

        return new static(base64_encode($json), $this->encoding);
    }

    /**
     * Decrypt the string.
     *
     * @param string $key    The key for decrypting
     * @param string $cipher The cipher method
     *
     * Supported cipher methods:
     *
     *   - AES-128-CBC (default)
     *   - AES-256-CBC
     *
     * @throws \PHLAK\Twine\Exceptions\DecryptionException
     *
     * @return self
     */
    public function decrypt(string $key, string $cipher = 'AES-128-CBC') : self
    {
        if (! $this->isEncrypted($cipher)) {
            throw new DecryptionException('The string is not an encrypted string');
        }

        $payload = json_decode(base64_decode($this->string));
        $expectedHmac = hash_hmac('sha256', $payload->iv . $payload->ciphertext, $key = md5($key));

        if (! hash_equals($payload->hmac, $expectedHmac)) {
            throw new DecryptionException('The HMAC is invalid');
        }

        $plaintext = openssl_decrypt($payload->ciphertext, $cipher, $key, 0, base64_decode($payload->iv));

        if ($plaintext === false) {
            throw new DecryptionException('Failed to decrypt the string');
        }

        return new static($plaintext, $this->encoding);
    }

    /**
     * Determine if the string is an encrypted string.
     *
     * @return bool True if the string is an encrypted string, otherwise false
     */
    protected function isEncrypted(string $cipher)
    {
        $payload = json_decode(base64_decode($this->string));

        return isset($payload->iv, $payload->ciphertext, $payload->hmac)
            && strlen(base64_decode($payload->iv)) === openssl_cipher_iv_length($cipher);
    }
}
