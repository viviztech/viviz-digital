<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.admin.pages.manage-settings';

    protected static ?string $slug = 'settings';

    protected static ?string $title = 'Settings';

    protected static ?string $navigationLabel = 'Settings';

    public array $settings = [];

    public function mount(): void
    {
        $allSettings = Setting::all();

        foreach ($allSettings as $setting) {
            $this->settings[$setting->key] = $setting->typed_value;
        }
    }

    public function form(Form $form): Form
    {
        $schema = [];
        $groupedSettings = Setting::all()->groupBy('group');

        foreach ($groupedSettings as $group => $settings) {
            $fields = [];

            foreach ($settings as $setting) {
                $field = match ($setting->type) {
                    'boolean' => Forms\Components\Toggle::make("{$setting->key}")
                        ->label($setting->label)
                        ->helperText($setting->description),
                    'integer' => Forms\Components\TextInput::make("{$setting->key}")
                        ->label($setting->label)
                        ->helperText($setting->description)
                        ->numeric(),
                    'json' => Forms\Components\Textarea::make("{$setting->key}")
                        ->label($setting->label)
                        ->helperText($setting->description)
                        ->rows(3),
                    default => Forms\Components\TextInput::make("{$setting->key}")
                        ->label($setting->label)
                        ->helperText($setting->description),
                };

                $fields[] = $field;
            }

            $schema[] = Forms\Components\Section::make(ucfirst($group))
                ->schema($fields)
                ->columns(2)
                ->collapsible();
        }

        return $form
            ->schema($schema)
            ->statePath('settings');
    }

    public function save(): void
    {
        foreach ($this->settings as $key => $value) {
            Setting::set($key, is_bool($value) ? ($value ? 'true' : 'false') : $value);
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }
}
