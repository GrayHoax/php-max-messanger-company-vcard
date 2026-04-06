<?php

declare(strict_types=1);

namespace VCardBot\Bot;

// PHPMaxBot and Bot are global-namespace classes (no namespace in the framework).
// Keyboard lives in PHPMaxBot\Helpers namespace.
use PHPMaxBot\Helpers\Keyboard;
use VCardBot\Config\ContentLoader;
use VCardBot\Formatter\MessageFormatter;

class VCardBot
{
    private \PHPMaxBot $bot;
    private ContentLoader $content;
    private MessageFormatter $formatter;

    public function __construct(string $token, ContentLoader $content)
    {
        $this->bot = new \PHPMaxBot($token);
        $this->bot->setFormat('markdown');
        $this->content = $content;
        $this->formatter = new MessageFormatter($content);
    }

    public function run(): void
    {
        $this->registerCommands();
        $this->registerCallbacks();
        $this->registerRequestHandler();
        $this->bot->start();
    }

    // -------------------------------------------------------------------------
    // Commands
    // -------------------------------------------------------------------------

    private function registerCommands(): void
    {
        $formatter = $this->formatter;

        $this->bot->command('start', function () use ($formatter) {
            \Bot::sendMessage(
                $formatter->welcomeMessage(),
                ['attachments' => [$this->buildMainMenu()]]
            );
        });

        $this->bot->command('help', function () use ($formatter) {
            \Bot::sendMessage($formatter->helpMessage());
        });
    }

    // -------------------------------------------------------------------------
    // Callback handlers
    // -------------------------------------------------------------------------

    private function registerCallbacks(): void
    {
        $formatter = $this->formatter;

        // О компании
        $this->bot->action('menu:about', function () use ($formatter) {
            $callbackId = \PHPMaxBot::$currentUpdate['callback']['callback_id'];
            $this->answerWithMessage($callbackId, $formatter->aboutMessage(), [$this->buildBackButton()]);
        });

        // Услуги
        $this->bot->action('menu:services', function () use ($formatter) {
            $callbackId = \PHPMaxBot::$currentUpdate['callback']['callback_id'];
            $this->answerWithMessage($callbackId, $formatter->servicesMessage(), [$this->buildBackButton()]);
        });

        // Портфолио
        $this->bot->action('menu:portfolio', function () use ($formatter) {
            $callbackId = \PHPMaxBot::$currentUpdate['callback']['callback_id'];
            $this->answerWithMessage($callbackId, $formatter->portfolioMessage(), [$this->buildBackButton()]);
        });

        // Контакты
        $this->bot->action('menu:contacts', function () use ($formatter) {
            $callbackId = \PHPMaxBot::$currentUpdate['callback']['callback_id'];
            $this->answerWithMessage($callbackId, $formatter->contactsMessage(), [$this->buildBackButton()]);
        });

        // Оставить заявку
        $this->bot->action('menu:request', function () use ($formatter) {
            $callbackId = \PHPMaxBot::$currentUpdate['callback']['callback_id'];
            $this->answerWithMessage($callbackId, $formatter->requestMessage(), [$this->buildRequestKeyboard()]);
        });

        // Назад в главное меню
        $this->bot->action('menu:main', function () use ($formatter) {
            $callbackId = \PHPMaxBot::$currentUpdate['callback']['callback_id'];
            $this->answerWithMessage($callbackId, $formatter->welcomeMessage(), [$this->buildMainMenu()]);
        });
    }

    // -------------------------------------------------------------------------
    // Free-text request handler
    // -------------------------------------------------------------------------

    private function registerRequestHandler(): void
    {
        $content = $this->content;
        $formatter = $this->formatter;

        $this->bot->on('message_created', function () use ($content, $formatter) {
            $text = \Bot::getText();

            // Ignore commands
            if (str_starts_with((string) $text, '/')) {
                return;
            }

            $requestCfg = $content->request();
            $managerChatId = $requestCfg['manager_chat_id'] ?? '';

            // Forward request to manager if chat_id is configured
            if (!empty($managerChatId)) {
                $sender = \Bot::getSender();
                $senderName = trim(($sender['name'] ?? '') . ' ' . ($sender['username'] ?? ''));
                $userId = $sender['user_id'] ?? 'unknown';

                $managerText = "📬 *Новая заявка*\n\n"
                    . "От: {$senderName} (ID: {$userId})\n\n"
                    . "*Сообщение:*\n{$text}";

                \Bot::sendMessageToChat((int) $managerChatId, $managerText);
            }

            $confirmation = $requestCfg['confirmation'] ?? '✅ Ваше сообщение принято!';
            \Bot::sendMessage(
                $confirmation,
                ['attachments' => [$this->buildMainMenu()]]
            );
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * answerOnCallback does not apply PHPMaxBot::getFormat() automatically,
     * so we inject 'format' into the message payload ourselves.
     */
    private function answerWithMessage(string $callbackId, string $text, array $attachments): void
    {
        \Bot::answerOnCallback($callbackId, [
            'message' => [
                'text' => $text,
                'format' => \PHPMaxBot::getFormat() ?: 'markdown',
                'attachments' => $attachments,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Keyboard builders
    // -------------------------------------------------------------------------

    private function buildMainMenu(): array
    {
        return Keyboard::inlineKeyboard([
            [
                Keyboard::callback('🏢 О компании', 'menu:about'),
                Keyboard::callback('🛠 Услуги', 'menu:services'),
            ],
            [
                Keyboard::callback('📁 Портфолио', 'menu:portfolio'),
                Keyboard::callback('📞 Контакты', 'menu:contacts'),
            ],
            [
                Keyboard::callback('📝 Оставить заявку', 'menu:request', ['intent' => 'positive']),
            ],
        ]);
    }

    private function buildBackButton(): array
    {
        return Keyboard::inlineKeyboard([
            [
                Keyboard::callback('← Главное меню', 'menu:main'),
            ],
        ]);
    }

    private function buildRequestKeyboard(): array
    {
        return Keyboard::inlineKeyboard([
            [
                Keyboard::requestContact('📱 Отправить контакт'),
            ],
            [
                Keyboard::callback('← Главное меню', 'menu:main'),
            ],
        ]);
    }
}
