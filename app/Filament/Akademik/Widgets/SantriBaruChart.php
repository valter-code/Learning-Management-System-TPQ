<?php

namespace App\Filament\Akademik\Widgets;

use Flowframe\Trend\Trend;
use App\Models\PendaftarSantri;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class SantriBaruChart extends ChartWidget
{
    protected static ?string $heading = 'Pendaftaran Santri Baru';
    protected static string $color = 'info';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $data = Trend::model(PendaftarSantri::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();


        return [
             'datasets' => [
                [
                    'label' => 'Santri Baru Terdaftar',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
