<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserThemeSetting;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        $themeSettings = auth()->user()->themeSettings ?? new UserThemeSetting();
        return view('theme.settings', compact('themeSettings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:light,dark,custom',
            'font_family' => 'required|string',
            'font_size' => 'required|in:small,medium,large',
            'primary_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'background_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'text_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'compact_mode' => 'boolean',
            'custom_css' => 'nullable|array'
        ]);

        $themeSettings = auth()->user()->themeSettings ?? new UserThemeSetting();
        $themeSettings->user_id = auth()->id();
        $themeSettings->fill($validated);
        $themeSettings->save();

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt giao diện đã được cập nhật',
            'settings' => $themeSettings->getThemeStyles()
        ]);
    }

    public function reset()
    {
        $themeSettings = auth()->user()->themeSettings;
        if ($themeSettings) {
            $themeSettings->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã đặt lại cài đặt giao diện về mặc định'
        ]);
    }
}
