<?php
function slugify($text) {
    // Supprime les caractères spéciaux et remplace les espaces par des tirets
    $text = preg_replace('~[^\p{L}\p{N}]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text;
}
function createSlug($string) {
    // Transforme en minuscules
    $slug = strtolower(trim($string));
    // Remplace les espaces par des tirets
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    // Retire les caractères non alphanumériques
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    return $slug;
}

function slugifyUser($text) {
    // Supprime les caractères spéciaux et remplace les espaces par des tirets
    $text = preg_replace('~[^\p{L}\p{N}]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text;
}