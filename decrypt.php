<?php

$tokenFile = __DIR__ . '/resource/password_token';
$output = __DIR__ . '/output/';
$input = __DIR__ . '/input/';

if(!file_exists($output)) {
    mkdir($output);
}

if(!file_exists($input)) {
    mkdir($output);
}

if (!file_exists($tokenFile)) {
    die("Error: password_token file not found.\n");
}

$token = trim(file_get_contents($tokenFile));
$inputFile = $input.$argv[1] ?? null;

if (!$inputFile || !file_exists($inputFile)) {
    die("Usage: php decrypt.php [target_file.enc]\n");
}

$cipher = 'aes-256-gcm';
$raw = file_get_contents($inputFile);

$iv = substr($raw, 0, 12);
$tag = substr($raw, 12, 16);
$ciphertext = substr($raw, 28);

$key = hash('sha256', $token, true);

$decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, "");

if ($decrypted === false) {
    die("Error: Decryption failed. Token mismatch or data corruption.\n");
}

$outputFile = str_replace('.enc', '', $inputFile);
file_put_contents($output.$outputFile, $decrypted);

echo "Successfully decrypted to {$outputFile}\n";