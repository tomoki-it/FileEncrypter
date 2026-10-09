<?php

$tokenFile = __DIR__ . '/resource/password_token';
$input = __DIR__ . '/input/';
$output = __DIR__ . '/output/';
if(!file_exists($output)) {
    mkdir($output);
}
if(!file_exists($input)) {
    mkdir($output);
}
if (!file_exists($tokenFile)) {
    if(!file_exists(__DIR__."/resource/")) {
        mkdir(__DIR__."/resource/");
    }
    file_put_contents($tokenFile, "");
    die("
    Error: password_token file not found.\n
    Info : A file /resource/password_token was generated successfully.\n
    ");
}

$token = trim(file_get_contents($tokenFile));
$inputFile = $argv[1] ?? null;
$delete = $argv[2] ?? null;

if (!$inputFile || !file_exists($input.$inputFile)) {
    die("Usage: php encrypt.php [target_file]\n");
}

$cipher = 'aes-256-gcm';
$iv = random_bytes(12);
$key = hash('sha256', $token, true);

$data = file_get_contents($input.$inputFile);

$ciphertext = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, "", 16);

if ($ciphertext === false) {
    die("Error: Encryption failed.\n");
}

$output = $iv . $tag . $ciphertext;
file_put_contents($output.$inputFile . '.enc', $output);

if($delete != null){
    unlink($input.$inputFile);
}

echo "Successfully encrypted to {$inputFile}.enc\n";