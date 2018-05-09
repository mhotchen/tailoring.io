<?php
namespace App\Spa;

final class UrlGenerator
{
    /** @var string */
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function generate(string $path, array $params = null): string
    {
        $template = '%s%s';
        $values = [$this->baseUrl, $path];

        if ($params) {
            $template .= '?%s';
            $values[] = $params;
        }

        return vsprintf($template, $values);
    }
}