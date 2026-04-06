<?php

declare(strict_types=1);

namespace VCardBot\Config;

use Symfony\Component\Yaml\Yaml;
use RuntimeException;

class ContentLoader
{
    private array $data;

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Content file not found: {$filePath}");
        }

        $this->data = Yaml::parseFile($filePath);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->data;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function company(): array
    {
        return $this->data['company'] ?? [];
    }

    public function services(): array
    {
        return $this->data['services'] ?? [];
    }

    public function portfolio(): array
    {
        return $this->data['portfolio'] ?? [];
    }

    public function contacts(): array
    {
        return $this->data['contacts'] ?? [];
    }

    public function request(): array
    {
        return $this->data['request'] ?? [];
    }

    public function botConfig(): array
    {
        return $this->data['bot'] ?? [];
    }

    public function author(): array
    {
        return $this->data['bot']['author'] ?? [];
    }

    /**
     * Replaces {placeholders} in a string with actual values.
     */
    public function interpolate(string $template, array $vars = []): string
    {
        $defaultVars = [
            'company_name' => $this->get('company.name', ''),
            'company_tagline' => $this->get('company.tagline', ''),
        ];

        $vars = array_merge($defaultVars, $vars);

        foreach ($vars as $key => $value) {
            $template = str_replace('{' . $key . '}', (string) $value, $template);
        }

        return $template;
    }
}
