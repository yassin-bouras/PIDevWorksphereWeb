<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CaptchaGenerator
{
    private const CAPTCHA_LENGTH = 6;
    private const CHARACTERS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function __construct(
        private SessionInterface $session
    ) {}

    public function generateCaptcha(): string
    {
        $captcha = $this->generateRandomString();
        $this->session->set('captcha', $captcha);
        return $captcha;
    }

    private function generateRandomString(): string
    {
        $captcha = '';
        $max = strlen(self::CHARACTERS) - 1;

        for ($i = 0; $i < self::CAPTCHA_LENGTH; $i++) {
            $captcha .= self::CHARACTERS[random_int(0, $max)];
        }

        return $captcha;
    }

    public function verifyCaptcha(string $input): bool
    {
        $storedCaptcha = $this->session->get('captcha');
        return $storedCaptcha && strtoupper(trim($input)) === $storedCaptcha;
    }

    public function getCurrentCaptcha(): ?string
    {
        return $this->session->get('captcha');
    }
}
