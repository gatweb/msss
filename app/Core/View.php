<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

class View
{
    private string $layout = 'default';
    private array $data = [];
    private string $title = '';
    private array $scripts = [];
    private string $phpViewPath;
    private string $twigViewPath;
    private ?Environment $twig = null;

    public function __construct()
    {
        $this->phpViewPath = defined('VIEW_PATH') ? VIEW_PATH : __DIR__ . '/../../app/views';
        $this->twigViewPath = defined('ROOT_PATH') ? ROOT_PATH . '/resources/views' : __DIR__ . '/../../resources/views';

        if (!is_dir($this->twigViewPath)) {
            mkdir($this->twigViewPath, 0777, true);
        }

        $this->initialiseTwig();
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function render(string $view, array $data = [], ?string $layout = null, bool $echoOutput = true)
    {
        $this->data = array_merge($this->data, $data);

        if ($layout !== null) {
            $this->layout = $layout;
        }

        $template = str_replace('..', '', str_replace('\\', '/', $view));
        $twigTemplate = ltrim($template, '/');
        $twigTemplate .= substr($twigTemplate, -5) === '.twig' ? '' : '.twig';

        if ($this->twig && $this->twig->getLoader()->exists($twigTemplate)) {
            $context = $this->data;
            $context['layout_template'] = $this->resolveLayoutTemplate();
            $context['pageTitle'] = $context['pageTitle'] ?? $this->title;

            $output = $this->twig->render($twigTemplate, $context);
            if ($echoOutput) {
                echo $output;
            }

            return $output;
        }

        return $this->renderPhp($template, $this->data, $echoOutput);
    }

    public function partial(string $partial, array $data = []): void
    {
        $partialFile = rtrim($this->phpViewPath, '/') . '/partials/' . $partial . '.php';
        if (!file_exists($partialFile)) {
            throw new \RuntimeException("Partial non trouvé : {$partial}");
        }

        extract(array_merge($this->data, $data));
        require $partialFile;
    }

    public function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function addScript(string $script): void
    {
        $this->scripts[] = $script;
    }

    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function setData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    private function initialiseTwig(): void
    {
        if (!class_exists(Environment::class)) {
            return;
        }

        $cacheDirectory = defined('CACHE_PATH') ? rtrim(CACHE_PATH, '/') . '/views' : __DIR__ . '/../../storage/cache/views';
        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }

        $loader = new FilesystemLoader($this->twigViewPath);
        $this->twig = new Environment($loader, [
            'cache' => $cacheDirectory,
            'auto_reload' => defined('APP_DEBUG') ? APP_DEBUG : false,
        ]);

        $this->registerTwigHelpers();
    }

    private function registerTwigHelpers(): void
    {
        if (!$this->twig) {
            return;
        }

        $this->twig->addGlobal('current_path', $_SERVER['REQUEST_URI'] ?? '/');
        $this->twig->addGlobal('app_debug', defined('APP_DEBUG') ? APP_DEBUG : false);

        $this->twig->addFunction(new TwigFunction('asset', static function (string $path): string {
            if (function_exists('asset')) {
                return asset($path);
            }

            return '/assets/' . ltrim($path, '/');
        }));

        $this->twig->addFunction(new TwigFunction('flash_messages', static function (): array {
            if (function_exists('flash_messages')) {
                return flash_messages();
            }

            return [];
        }));

        $this->twig->addFunction(new TwigFunction('flash_class', static function (string $type): string {
            if (function_exists('flash_class')) {
                return flash_class($type);
            }

            return 'alert-info';
        }));

        $this->twig->addFunction(new TwigFunction('pagination', static function (int $current, int $total, array $params = []): array {
            if (function_exists('paginate')) {
                return paginate($current, $total, $params);
            }

            return [
                'current' => $current,
                'total' => $total,
                'has_previous' => $current > 1,
                'has_next' => $current < $total,
                'previous_url' => null,
                'next_url' => null,
                'pages' => [],
            ];
        }));

        $this->twig->addFunction(new TwigFunction('is_active_path', static function (string $uri, bool $exact = false): bool {
            if (function_exists('is_active_path')) {
                return is_active_path($uri, null, $exact);
            }

            $current = $_SERVER['REQUEST_URI'] ?? '/';
            return $exact ? $current === $uri : str_starts_with($current, $uri);
        }));

        $this->twig->addFilter(new TwigFilter('money', static function ($value, string $currency = '€', int $decimals = 2) {
            if (function_exists('format_money')) {
                return format_money($value, $currency, $decimals);
            }

            return number_format((float) $value, $decimals, ',', ' ') . ' ' . $currency;
        }));
    }

    private function resolveLayoutTemplate(): string
    {
        $layout = $this->layout ?: 'base';
        if (str_ends_with($layout, '.twig')) {
            return $layout;
        }

        $candidate = 'layouts/' . $layout . '.twig';

        if ($this->twig && $this->twig->getLoader()->exists($candidate)) {
            return $candidate;
        }

        return 'layouts/base.twig';
    }

    private function renderPhp(string $template, array $data, bool $echoOutput)
    {
        $viewFile = rtrim($this->phpViewPath, '/') . '/' . $template . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vue non trouvée : {$template}");
        }

        extract($data);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($this->layout) {
            $layoutFile = rtrim($this->phpViewPath, '/') . '/layouts/' . $this->layout . '.php';
            if (file_exists($layoutFile)) {
                $layoutData = array_merge($data, ['content' => $content]);
                extract($layoutData);
                ob_start();
                require $layoutFile;
                $content = ob_get_clean();
            }
        }

        if ($echoOutput) {
            echo $content;
        }

        return $content;
    }
}
