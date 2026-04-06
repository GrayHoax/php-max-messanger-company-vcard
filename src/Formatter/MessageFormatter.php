<?php

declare(strict_types=1);

namespace VCardBot\Formatter;

use VCardBot\Config\ContentLoader;

class MessageFormatter
{
    private ContentLoader $content;

    public function __construct(ContentLoader $content)
    {
        $this->content = $content;
    }

    public function welcomeMessage(): string
    {
        $template = $this->content->get('bot.welcome_message', 'Добро пожаловать!');
        return $this->content->interpolate($template);
    }

    public function aboutMessage(): string
    {
        $company = $this->content->company();
        $name = $company['name'] ?? '';
        $tagline = $company['tagline'] ?? '';
        $about = trim($company['about'] ?? '');
        $founded = $company['founded'] ?? '';
        $employees = $company['employees'] ?? '';

        $lines = ["🏢 *{$name}*"];

        if ($tagline) {
            $lines[] = "_{$tagline}_";
        }

        $lines[] = '';
        $lines[] = $about;

        if ($founded || $employees) {
            $lines[] = '';
        }

        if ($founded) {
            $lines[] = "📅 На рынке с {$founded} года";
        }

        if ($employees) {
            $lines[] = "👥 Сотрудников: {$employees}";
        }

        return implode("\n", $lines);
    }

    public function servicesMessage(): string
    {
        $services = $this->content->services();
        $title = $services['title'] ?? 'Наши услуги';
        $intro = $services['intro'] ?? '';
        $items = $services['items'] ?? [];

        $lines = ["🛠 *{$title}*"];

        if ($intro) {
            $lines[] = '';
            $lines[] = $intro;
        }

        if (!empty($items)) {
            $lines[] = '';
            foreach ($items as $index => $item) {
                $num = $index + 1;
                $name = $item['name'] ?? '';
                $desc = $item['description'] ?? '';
                $price = $item['price'] ?? '';

                $lines[] = "*{$num}. {$name}*";

                if ($desc) {
                    $lines[] = $desc;
                }

                if ($price) {
                    $lines[] = "💰 {$price}";
                }

                $lines[] = '';
            }
        }

        return trim(implode("\n", $lines));
    }

    public function portfolioMessage(): string
    {
        $portfolio = $this->content->portfolio();
        $title = $portfolio['title'] ?? 'Портфолио';
        $intro = $portfolio['intro'] ?? '';
        $items = $portfolio['items'] ?? [];

        $lines = ["📁 *{$title}*"];

        if ($intro) {
            $lines[] = '';
            $lines[] = $intro;
        }

        if (!empty($items)) {
            $lines[] = '';
            foreach ($items as $index => $item) {
                $num = $index + 1;
                $title_item = $item['title'] ?? '';
                $desc = $item['description'] ?? '';
                $year = $item['year'] ?? '';
                $url = $item['url'] ?? '';

                $header = "*{$num}. {$title_item}*";
                if ($year) {
                    $header .= " ({$year})";
                }

                $lines[] = $header;

                if ($desc) {
                    $lines[] = $desc;
                }

                if ($url) {
                    $lines[] = "🔗 {$url}";
                }

                $lines[] = '';
            }
        }

        return trim(implode("\n", $lines));
    }

    public function contactsMessage(): string
    {
        $contacts = $this->content->contacts();
        $title = $contacts['title'] ?? 'Контакты';

        $lines = ["📞 *{$title}*", ''];

        if (!empty($contacts['phone'])) {
            $lines[] = "📱 Телефон: {$contacts['phone']}";
        }

        if (!empty($contacts['email'])) {
            $lines[] = "✉️ Email: {$contacts['email']}";
        }

        if (!empty($contacts['website'])) {
            $lines[] = "🌐 Сайт: {$contacts['website']}";
        }

        if (!empty($contacts['address'])) {
            $lines[] = "📍 Адрес: {$contacts['address']}";
        }

        if (!empty($contacts['working_hours'])) {
            $lines[] = "🕐 Режим работы: {$contacts['working_hours']}";
        }

        $social = $contacts['social'] ?? [];
        if (!empty(array_filter($social))) {
            $lines[] = '';
            $lines[] = '*Мы в социальных сетях:*';

            if (!empty($social['vk'])) {
                $lines[] = "VK: {$social['vk']}";
            }
            if (!empty($social['max'])) {
                $lines[] = "Max: {$social['max']}";
            }
            if (!empty($social['telegram'])) {
                $lines[] = "Telegram: {$social['telegram']}";
            }
        }

        return implode("\n", $lines);
    }

    public function requestMessage(): string
    {
        $request = $this->content->request();
        $title = $request['title'] ?? 'Оставить заявку';
        $intro = $request['intro'] ?? '';
        $prompt = $request['prompt'] ?? '';

        $lines = ["📝 *{$title}*"];

        if ($intro) {
            $lines[] = '';
            $lines[] = $intro;
        }

        if ($prompt) {
            $lines[] = '';
            $lines[] = $prompt;
        }

        return implode("\n", $lines);
    }

    public function helpMessage(): string
    {
        $author = $this->content->author();
        $companyName = $this->content->get('company.name', 'нашей компании');

        $lines = [
            "ℹ️ *Справка по боту-визитке*",
            '',
            "Этот бот создан для представления {$companyName} в мессенджере Max.",
            '',
            '*Доступные команды:*',
            '/start — Главное меню',
            '/help — Эта справка',
            '',
            '*По вопросам работы бота обращайтесь к автору:*',
        ];

        if (!empty($author['name'])) {
            $lines[] = "👤 {$author['name']}";
        }

        if (!empty($author['max'])) {
            $lines[] = "Max: {$author['max']}";
        }

        if (!empty($author['telegram'])) {
            $lines[] = "Telegram: {$author['telegram']}";
        }

        if (!empty($author['email'])) {
            $lines[] = "✉️ {$author['email']}";
        }

        if (!empty($author['github'])) {
            $lines[] = "GitHub: {$author['github']}";
        }

        return implode("\n", $lines);
    }
}
