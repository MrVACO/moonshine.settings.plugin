# Service "Settings" for Moonshine Admin Panel (Laravel)

Simplest storage of settings in the database

## Installation

```bash
composer require mr-vaco/moonshine.settings.plugin
```

```bash
php artisan vendor:publish --tag=settings
```

```bash
php artisan migrate
```

## How to use

When entering the data, the data type (string / integer / boolean / array / json) is automatically determined and the last entered type is used - data obtaining ```$settings->value```.

You can indicate any type of indicated, but keep in mind that when you call ```$settings->value``` will receive only the value specified in the "type" column:

```php
$settings->json;
$settings->string;
$settings->integer;
$settings->boolean;
```

### Get data

```php
use MrVaco\Moonshine\Settings\Models\Settings;
use MrVaco\Moonshine\Settings\SettingsService;

class Example extends Page
{
    /*
     * Necessarily! indicate the key to identification
     * You can specify any string value
     */
    protected string $settingsKey = 'example_key';
    protected $settings = [];

    public function __construct(CoreContract $core, protected SettingsService $settingsService)
    {
        parent::__construct($core);
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $settings = $this->settingsService->get($this->settingsKey); // Get data on the key
        $this->settings = $settings->value; // Get a meaning
    }
    
    // An example of a form:
    protected function fields()
    {
        return is_array($this->settings)
            ? FormBuilder::make()
                ->fillCast($this->settings, new ModelCaster(Settings::class))
                ->fields([])
            : Text::make('example');
}
```

### Save data

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
            $this->settingsService->set($this->settingsKey, $data); // Retain the meaning
        } catch (\Exception $e)
        {
            info($e);
        }
    }
}
```
