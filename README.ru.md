# Сервис "Настройки" для Moonshine Admin Panel (Laravel)

Простейшее хранение настроек в базе данных

## Установка

```bash
composer require mr-vaco/moonshine.settings.plugin
```

```bash
php artisan vendor:publish --tag=settings
```

```bash
php artisan migrate
```

## Как использовать

При внесении данных автоматически определяется тип данных (строка / целое число / логический / массив / json) и используется последний внесённый тип - получение данных ```$settings->value```.

Можете указывать любой тип из указанных, но учтите, что при вызове ```$settings->value``` будет получать только значение типа, указанного в колонке "type":

```php
$settings->json;
$settings->string;
$settings->integer;
$settings->boolean;
```

### Получить данные

```php
use MrVaco\Moonshine\Settings\Models\Settings;use MrVaco\Moonshine\Settings\SettingsService;

class Example extends Page
{
    /*
     * Обязательно! указать ключ для идентификации
     * Можно указать любое строковое значение
     */
    protected string $settingsKey = 'example_key';
    protected $settings = [];

    // Подтягиваем данные
    public function __construct(CoreContract $core, protected SettingsService $settingsService)
    {
        parent::__construct($core);
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $settings = $this->settingsService->get($this->settingsKey); // Получаем данные по ключу
        $this->settings = $settings->value; // Получаем значение
    }
    
    // Пример формы:
    protected function fields()
    {
        return is_array($this->settings)
            ? FormBuilder::make()
                ->fillCast($this->settings, new ModelCaster(Settings::class))
                ->fields([])
            : Text::make('example');
}
```

### Сохранение данных

```php
use MrVaco\Moonshine\Settings\SettingsService;

class Example extends Page
{
    protected string $settingsKey = 'example_key';

    public function __construct(CoreContract $core, protected SettingsService $settingsService)
    {
        parent::__construct($core);
    }

    public function exampleMethod(array|string|int|bool $data)
    {
        try
        {
            $this->settingsService->set($this->settingsKey, $data); // Сохраняем значение
        } catch (\Exception $e)
        {
            info($e);
        }
    }
}
```
