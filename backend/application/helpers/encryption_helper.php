<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Fungsi untuk mengenkripsi ID menggunakan AES
function encryptID($id, $key='secret') {
    $cipher = "aes-256-cbc";
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($id, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    // $encoded_encrypted = base64_encode($encrypted . '::' . $iv);
    $encoded_encrypted = str_replace('/', '-', base64_encode($encrypted . '::' . $iv));
    return urlencode($encoded_encrypted);
}

// Fungsi untuk mendekripsi ID menggunakan AES
function decryptID($encrypted_id, $key='secret') {
    $cipher = "aes-256-cbc";
    $encrypted_id = str_replace('-', '/', $encrypted_id);
    $decoded_encrypted = base64_decode(urldecode($encrypted_id));
    list($encrypted_data, $iv) = explode('::', $decoded_encrypted, 2);
    $decrypted = openssl_decrypt($encrypted_data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
}
