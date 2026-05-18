<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    public const LANGUAGES = [
        'af' => 'Afrikaans', 'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic', 'hy' => 'Armenian', 'az' => 'Azerbaijani', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali', 'bs' => 'Bosnian', 'bg' => 'Bulgarian', 'ca' => 'Catalan', 'ceb' => 'Cebuano', 'ny' => 'Chichewa', 'zh' => 'Chinese (Simplified)', 'zh-TW' => 'Chinese (Traditional)', 'co' => 'Corsican', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'tl' => 'Filipino', 'fi' => 'Finnish', 'fr' => 'French', 'fy' => 'Frisian', 'gl' => 'Galician', 'ka' => 'Georgian', 'de' => 'German', 'el' => 'Greek', 'gu' => 'Gujarati', 'ht' => 'Haitian Creole', 'ha' => 'Hausa', 'haw' => 'Hawaiian', 'iw' => 'Hebrew', 'hi' => 'Hindi', 'hmn' => 'Hmong', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'ig' => 'Igbo', 'id' => 'Indonesian', 'ga' => 'Irish', 'it' => 'Italian', 'ja' => 'Japanese', 'jw' => 'Javanese', 'kn' => 'Kannada', 'kk' => 'Kazakh', 'km' => 'Khmer', 'ko' => 'Korean', 'ku' => 'Kurdish (Kurmanji)', 'ky' => 'Kyrgyz', 'lo' => 'Lao', 'la' => 'Latin', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'lb' => 'Luxembourgish', 'mk' => 'Macedonian', 'mg' => 'Malagasy', 'ms' => 'Malay', 'ml' => 'Malayalam', 'mt' => 'Maltese', 'mi' => 'Maori', 'mr' => 'Marathi', 'mn' => 'Mongolian', 'my' => 'Myanmar (Burmese)', 'ne' => 'Nepali', 'no' => 'Norwegian', 'ps' => 'Pashto', 'fa' => 'Persian', 'pl' => 'Polish', 'pt' => 'Portuguese', 'pa' => 'Punjabi', 'ro' => 'Romanian', 'ru' => 'Russian', 'sm' => 'Samoan', 'gd' => 'Scots Gaelic', 'sr' => 'Serbian', 'st' => 'Sesotho', 'sn' => 'Shona', 'sd' => 'Sindhi', 'si' => 'Sinhala', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'so' => 'Somali', 'es' => 'Spanish', 'su' => 'Sundanese', 'sw' => 'Swahili', 'sv' => 'Swedish', 'tg' => 'Tajik', 'ta' => 'Tamil', 'te' => 'Telugu', 'th' => 'Thai', 'tr' => 'Turkish', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek', 'vi' => 'Vietnamese', 'cy' => 'Welsh', 'xh' => 'Xhosa', 'yi' => 'Yiddish', 'yo' => 'Yoruba', 'zu' => 'Zulu'
    ];

    private function getLanguages()
    {
        $langs = Setting::get('system_language', '[]');
        return is_string($langs) ? json_decode($langs, true) : (is_array($langs) ? $langs : []);
    }

    private function saveLanguages(array $langs)
    {
        Setting::set('system_language', json_encode($langs, JSON_UNESCAPED_UNICODE), 'general');
        \Illuminate\Support\Facades\Cache::forget('api_settings_general');
    }

    public function index()
    {
        $languages = $this->getLanguages();
        
        // Auto-initialize default language if empty
        if (empty($languages)) {
            $languages = [
                [
                    'id' => time(),
                    'code' => 'vi',
                    'name' => 'Vietnamese',
                    'direction' => 'ltr',
                    'status' => 1,
                    'default' => true,
                ]
            ];
            $this->saveLanguages($languages);
            
            // Create default vi.json if not exists
            $defaultLangPath = resource_path("lang/vi.json");
            if (!File::exists($defaultLangPath)) {
                if (!File::isDirectory(resource_path('lang'))) {
                    File::makeDirectory(resource_path('lang'), 0755, true, true);
                }
                File::put($defaultLangPath, json_encode([
                    'home' => 'Trang chủ',
                    'login' => 'Đăng nhập',
                    'search' => 'Tìm kiếm'
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }
        }

        $availableLanguages = self::LANGUAGES;
        return view('admin.languages.index', compact('languages', 'availableLanguages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'direction' => 'required|in:ltr,rtl',
        ]);

        $languages = $this->getLanguages();
        
        foreach ($languages as $lang) {
            if ($lang['code'] === $request->code) {
                return redirect()->back()->with('error', 'Mã ngôn ngữ đã tồn tại!');
            }
        }

        $langName = self::LANGUAGES[$request->code] ?? strtoupper($request->code);

        $newLang = [
            'id' => time(),
            'code' => $request->code,
            'name' => $langName,
            'direction' => $request->direction,
            'status' => 1,
            'default' => count($languages) === 0,
        ];

        $languages[] = $newLang;
        $this->saveLanguages($languages);

        $newLangPath = resource_path("lang/{$request->code}.json");
        $defaultLangPath = resource_path("lang/vi.json");

        if (!File::exists($newLangPath) && File::exists($defaultLangPath)) {
            File::copy($defaultLangPath, $newLangPath);
        } elseif (!File::exists($newLangPath)) {
            File::put($newLangPath, '{}');
        }

        return redirect()->route('admin.languages.index')->with('success', 'Thêm ngôn ngữ thành công');
    }

    public function update(Request $request, $id)
    {
        $languages = $this->getLanguages();
        $updated = false;

        foreach ($languages as &$lang) {
            if ($lang['id'] == $id || $lang['code'] == $id) {
                if ($request->has('name')) $lang['name'] = $request->name;
                if ($request->has('direction')) $lang['direction'] = $request->direction;
                if ($request->has('status')) $lang['status'] = $request->status;
                if ($request->has('default') && $request->default) {
                    foreach ($languages as &$other) {
                        $other['default'] = false;
                    }
                    $lang['default'] = true;
                }
                $updated = true;
                break;
            }
        }

        if ($updated) {
            $this->saveLanguages($languages);
            return redirect()->back()->with('success', 'Cập nhật ngôn ngữ thành công');
        }

        return redirect()->back()->with('error', 'Không tìm thấy ngôn ngữ');
    }

    public function destroy($id)
    {
        $languages = $this->getLanguages();
        $filtered = array_filter($languages, function($lang) use ($id) {
            return $lang['id'] != $id && $lang['code'] != $id;
        });

        if (count($filtered) === count($languages)) {
            return redirect()->back()->with('error', 'Không tìm thấy ngôn ngữ');
        }

        $this->saveLanguages(array_values($filtered));
        return redirect()->back()->with('success', 'Xóa ngôn ngữ thành công');
    }

    // --- Translation Keys Management --- //

    public function translations($code)
    {
        $path = resource_path("lang/{$code}.json");
        $defaultPath = resource_path("lang/vi.json");
        
        $translations = File::exists($path) ? json_decode(File::get($path), true) : [];
        $defaultTranslations = File::exists($defaultPath) ? json_decode(File::get($defaultPath), true) : [];
        
        // Merge to show missing keys from default file
        foreach ($defaultTranslations as $key => $val) {
            if (!isset($translations[$key])) {
                $translations[$key] = $val;
            }
        }

        return view('admin.languages.translations', compact('translations', 'code', 'defaultTranslations'));
    }

    public function updateTranslation(Request $request, $code)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable|string',
        ]);

        $path = resource_path("lang/{$code}.json");
        $translations = File::exists($path) ? json_decode(File::get($path), true) : [];
        $translations[$request->key] = $request->value ?? '';

        File::put($path, json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return response()->json(['success' => true]);
    }

    // Single Auto Translate
    public function translateSingle(Request $request, $code)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $defaultPath = resource_path("lang/vi.json");
        $defaultTranslations = File::exists($defaultPath) ? json_decode(File::get($defaultPath), true) : [];
        
        $originalText = $defaultTranslations[$request->key] ?? '';
        if (empty($originalText)) {
            return response()->json(['success' => false, 'message' => 'No original text found']);
        }

        $translated = auto_translator($originalText, 'vi', $code);

        // Save it immediately
        $path = resource_path("lang/{$code}.json");
        $translations = File::exists($path) ? json_decode(File::get($path), true) : [];
        $translations[$request->key] = $translated;
        File::put($path, json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return response()->json(['success' => true, 'translated' => $translated]);
    }

    // Chunked Translate All
    public function autoTranslate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $targetLang = $request->code;
        $path = resource_path("lang/{$targetLang}.json");
        $defaultPath = resource_path("lang/vi.json");

        if (!File::exists($defaultPath)) {
            return response()->json(['success' => false, 'message' => 'File ngôn ngữ mặc định vi.json không tồn tại'], 400);
        }

        $defaultTranslations = json_decode(File::get($defaultPath), true) ?? [];
        $targetTranslations = File::exists($path) ? (json_decode(File::get($path), true) ?? []) : [];

        // Lọc ra các từ chưa dịch
        $pendingKeys = [];
        foreach ($defaultTranslations as $key => $value) {
            if (empty($targetTranslations[$key]) || $targetTranslations[$key] === $value) {
                $pendingKeys[] = $key;
            }
        }

        if (count($pendingKeys) === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Đã dịch xong toàn bộ',
                'progress' => 100,
                'completed' => true
            ]);
        }

        // Lấy 20 từ đầu tiên
        $chunk = array_slice($pendingKeys, 0, 20);

        foreach ($chunk as $key) {
            $value = $defaultTranslations[$key];
            $translated = auto_translator($value, 'vi', $targetLang);
            if ($translated) {
                $targetTranslations[$key] = $translated;
            }
        }

        File::put($path, json_encode($targetTranslations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $remaining = count($pendingKeys) - count($chunk);
        if ($remaining < 0) $remaining = 0;
        
        $total = count($defaultTranslations);
        $processed = $total - $remaining;
        $progress = round(($processed / $total) * 100);

        return response()->json([
            'success' => true,
            'message' => "Đang dịch... {$progress}%",
            'progress' => $progress,
            'completed' => $remaining <= 0
        ]);
    }

    // Dịch tự động các bảng dữ liệu (Database)
    public function autoTranslateData(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'model' => 'required|string',
            'page' => 'required|integer|min:1'
        ]);

        $supportedModels = [
            'blog' => \App\Models\Blog::class,
            'blog_category' => \App\Models\BlogCategory::class,
            'slider' => \App\Models\Slider::class,
            'slider_item' => \App\Models\SliderItem::class,
        ];

        if (!array_key_exists($request->model, $supportedModels)) {
            return response()->json(['success' => false, 'message' => 'Model không hỗ trợ'], 400);
        }

        $modelClass = $supportedModels[$request->model];
        $targetLang = $request->code;
        
        // Phân trang 5 bản ghi mỗi lần gọi để tránh timeout Google API
        $records = $modelClass::orderBy('id')->paginate(5, ['*'], 'page', $request->page);

        if ($records->isEmpty()) {
            return response()->json([
                'success' => true,
                'completed' => true,
                'message' => 'Hoàn tất dịch bảng ' . $request->model
            ]);
        }

        foreach ($records as $record) {
            $changed = false;
            if (isset($record->translatable) && is_array($record->translatable)) {
                foreach ($record->translatable as $field) {
                    $original = $record->getTranslation($field, 'vi', false);
                    $current = $record->getTranslation($field, $targetLang, false);

                    // Nếu tiếng Việt có dữ liệu nhưng tiếng đích chưa có hoặc giống y hệt (chưa dịch)
                    if (!empty($original) && (empty($current) || $current === $original)) {
                        $translated = auto_translator($original, 'vi', $targetLang);
                        if ($translated) {
                            $record->setTranslation($field, $targetLang, $translated);
                            $changed = true;
                        }
                    }
                }
            }

            if ($changed) {
                // Tạm thời tắt timestamps để không ảnh hưởng đến updated_at nếu không cần thiết
                $record->timestamps = false;
                $record->save();
            }
        }

        $progress = round(($records->currentPage() / $records->lastPage()) * 100);

        return response()->json([
            'success' => true,
            'completed' => false,
            'progress' => $progress,
            'current_page' => $records->currentPage(),
            'last_page' => $records->lastPage(),
            'message' => "Đang dịch {$request->model}... {$progress}%"
        ]);
    }
}
