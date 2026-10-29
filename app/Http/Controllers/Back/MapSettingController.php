<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ApplicationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MapSettingController extends Controller
{
    public function index()
    {
        $provider = $this->currentProvider();
        $settingsTableReady = $this->settingsTableReady();

        return view('back.mapsetting.index', compact('provider', 'settingsTableReady'));
    }

    public function update(Request $request)
    {
        if (!$this->settingsTableReady()) {
            return redirect()
                ->route('MapSetting.index')
                ->with([
                    'message' => 'Map setting table is not ready. Please run php artisan migrate.',
                    'class' => 'warning',
                ]);
        }

        $validated = $request->validate([
            'map_provider' => ['required', 'in:google,open'],
        ], [
            'map_provider.required' => 'กรุณาเลือก Map Provider',
            'map_provider.in' => 'Map Provider ไม่ถูกต้อง',
        ]);

        ApplicationSetting::updateOrCreate(
            ['key' => 'map_provider'],
            [
                'value' => $validated['map_provider'],
                'modified_by' => Auth::id(),
            ]
        );

        config(['services.map_provider' => $validated['map_provider']]);

        return redirect()
            ->route('MapSetting.index')
            ->with([
                'message' => 'บันทึกการตั้งค่าแผนที่เรียบร้อย',
                'class' => 'success',
            ]);
    }

    private function currentProvider(): string
    {
        if (!$this->settingsTableReady()) {
            return 'google';
        }

        try {
            $provider = ApplicationSetting::where('key', 'map_provider')->value('value');
        } catch (\Throwable $exception) {
            return 'google';
        }

        return in_array($provider, ['google', 'open'], true) ? $provider : 'google';
    }

    private function settingsTableReady(): bool
    {
        try {
            return Schema::hasTable('application_settings');
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
