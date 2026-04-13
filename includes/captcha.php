<?php

function generate_captcha(): string
{
    $a = random_int(1, 9);
    $b = random_int(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    return "$a + $b = ?";
}

function validate_captcha(?string $value): bool
{
    if (!isset($_SESSION['captcha_answer'])) {
        return false;
    }

    $ok = ((int)$value === (int)$_SESSION['captcha_answer']);
    unset($_SESSION['captcha_answer']);

    return $ok;
}
