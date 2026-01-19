# Сервис "Настройки" для Moonshine Admin Panel (Laravel)

Простейшее хранение настроек в базе данных

| Moonshine | Версия пакета |
|-----------|---------------|
| 3+        | 1.x           |
| 4+        | 2.x           |

## Установка

```bash
composer require mr-vaco/moonshine.settings.plugin
```

```bash
php artisan migrate
```

## Как использовать

При внесении данных автоматически определяется тип данных (строка / целое число / логический / массив / json) и используется последний внесённый тип - получение данных ```$settings->value```.

Можете указывать любой тип из указанных, но учтите, что при вызове ```$settings->value``` будет получать только значение типа, указанного в колонке "type".

### Получить данные

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
     * Обязательно! указать ключ для идентификации
     * Можно указать любое строковое значение
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

### Сохранение данных

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
