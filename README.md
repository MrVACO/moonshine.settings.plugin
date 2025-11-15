# Service "Settings" for Moonshine Admin Panel (Laravel)

Simplest storage of settings in the database

| Moonshine | Package version |
|-----------|-----------------|
| 3+        | 1.x             |
| 4+        | 2.x             |

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

You can indicate any type of indicated, but keep in mind that when you call ```$settings->value``` will receive only the value specified in the "type" column.

### Get data

```php
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\FormBuilder;
use MrVaco\Moonshine\Settings\Models\Settings;
use MrVaco\Moonshine\Settings\SettingsService;

class ExamplePage extends Page
{
    /*
     * Necessarily! indicate the key to identification
     * You can specify any string value
     */
    protected string $settingsKey = 'example_key';
    protected array $settings = [];

    public function __construct(CoreContract $core, protected SettingsService $settingsService)
    {
        parent::__construct($core);

        $settings = $this->settingsService->get($this->settingsKey);
        $this->settings['data'] = $settings->value ?? null;
    }

    protected function components(): iterable
    {
        return [
            FormBuilder::make()
                ->fillCast($this->settings, new ModelCaster(Settings::class))
                ->fields([
                    Grid::make([
                        Column::make([
                            Fieldset::make('', [
                                Text::make(__('Site Name'), 'data.site.name'),

                                Text::make(__('Short name'), 'data.site.shortname'),

                                Text::make(__('Slogan'), 'data.site.slogan'),
                            ]),
                        ], colSpan: 4),
                    ]),
                ])
        ];
    }
}
```

### Save data

```php
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\FormBuilder;
use MrVaco\Moonshine\Settings\Models\Settings;
use MrVaco\Moonshine\Settings\SettingsService;

class ExamplePage extends Page
{
    protected string $settingsKey = 'example_key';
    protected array $settings = [];

    public function __construct(CoreContract $core, protected SettingsService $settingsService)
    {
        parent::__construct($core);

        $settings = $this->settingsService->get($this->settingsKey);
        $this->settings['data'] = $settings->value ?? null;
    }

    protected function components(): iterable
    {
        return [
            FormBuilder::make()
                ->asyncMethod('store')
                ->fillCast($this->settings, new ModelCaster(Settings::class))
                ->fields([
                    Grid::make([
                        Column::make([
                            Fieldset::make('', [
                                Text::make(__('Site Name'), 'data.site.name'),

                                Text::make(__('Short name'), 'data.site.shortname'),

                                Text::make(__('Slogan'), 'data.site.slogan'),
                            ]),

                            Fieldset::make(__('Phones'), [
                                Json::make('', 'data.phones')
                                    ->fields([
                                        Text::make("Number", 'number')
                                            ->placeholder('9 999 999 99 99'),

                                        Text::make("Mask", 'mask')
                                            ->placeholder('+9 (999) 999-99-99'),
                                    ])
                                    ->creatable(
                                        button: ActionButton::make(__('moonshine::ui.add'))->icon('plus')
                                    )
                                    ->removable(),
                            ]),
                        ], colSpan: 4),

                        Column::make([
                            Fieldset::make(__('Addition'), [
                                Json::make('', 'data.company.additions')
                                    ->fields([
                                        Text::make('Decryption', 'decryption'),

                                        Text::make('Value', 'value'),
                                    ])
                                    ->creatable(
                                        button: ActionButton::make(__('moonshine::ui.add'))->icon('plus')
                                    )
                                    ->reorderable()
                                    ->removable(),
                            ]),
                        ], colSpan: 4),
                    ]),
                ])
        ];
    }

    #[AsyncMethod]
    public function store(): JsonResponse
    {
        $message = __('moonshine::ui.saved');
        $type = ToastType::SUCCESS;

        try
        {
            $this->settingsService->set($this->settingsKey, request()->input('data'));
        } catch (\Exception $e)
        {
            info($e->getMessage());
            $message = __('moonshine::ui.saved_error');
            $type = ToastType::ERROR;
        }

        return JsonResponse::make()->toast($message, type: $type);
    }
}
```
