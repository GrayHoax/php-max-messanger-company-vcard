<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use VCardBot\Bot\VCardBot;
use VCardBot\Config\ContentLoader;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Validate required environment variables
$token = getenv('BOT_TOKEN') ?? '';

if (empty($token)) {
    fwrite(STDERR, "Error: BOT_TOKEN is not set. Copy .env.example to .env and fill in your bot token.\n");
    exit(1);
}

// Load content configuration
$contentFile = getenv('CONTENT_FILE') ?? __DIR__ . '/config/content.yaml';

if (!str_starts_with($contentFile, '/') && !preg_match('/^[A-Za-z]:/', $contentFile)) {
    $contentFile = __DIR__ . '/' . $contentFile;
}

try {
    $content = new ContentLoader($contentFile);
} catch (RuntimeException $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}

// Run the bot
$bot = new VCardBot($token, $content);
$bot->run();
