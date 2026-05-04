<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DefaultAvatarController extends Controller
{
    protected $name;
    protected $length = 2;
    protected $fontSize = 0.45;
    protected $size = 200;
    protected $background = "5e35b1";
    protected $color = "ffffff";
    protected $uppercase = true;
    protected $rounded = true;

    protected $colors = [
        ["b" => "5e35b1", "t" => "FFFFFF",], ["b" => "512da8", "t" => "FFFFFF",], ["b" => "4527a0", "t" => "FFFFFF",],
        ["b" => "311b92", "t" => "FFFFFF",], ["b" => "8e24aa", "t" => "FFFFFF",], ["b" => "7b1fa2", "t" => "FFFFFF",],
        ["b" => "6a1b9a", "t" => "FFFFFF",], ["b" => "4a148c", "t" => "FFFFFF",], ["b" => "3949ab", "t" => "FFFFFF",],
        ["b" => "303f9f", "t" => "FFFFFF",], ["b" => "283593", "t" => "FFFFFF",], ["b" => "1a237e", "t" => "FFFFFF",],
        ["b" => "1e88e5", "t" => "FFFFFF",], ["b" => "1976d2", "t" => "FFFFFF",], ["b" => "1565c0", "t" => "FFFFFF",],
        ["b" => "0d47a1", "t" => "FFFFFF",], ["b" => "039be5", "t" => "FFFFFF",], ["b" => "0288d1", "t" => "FFFFFF",],
        ["b" => "0277bd", "t" => "FFFFFF",], ["b" => "01579b", "t" => "FFFFFF",], ["b" => "00acc1", "t" => "FFFFFF",],
        ["b" => "0097a7", "t" => "FFFFFF",], ["b" => "00838f", "t" => "FFFFFF",], ["b" => "006064", "t" => "FFFFFF",],
        ["b" => "00897b", "t" => "FFFFFF",], ["b" => "00796b", "t" => "FFFFFF",], ["b" => "00695c", "t" => "FFFFFF",],
        ["b" => "004d40", "t" => "FFFFFF",], ["b" => "4caf50", "t" => "FFFFFF",], ["b" => "388e3c", "t" => "FFFFFF",],
        ["b" => "2e7d32", "t" => "FFFFFF",], ["b" => "1b5e20", "t" => "FFFFFF",], ["b" => "8bc34a", "t" => "FFFFFF",],
        ["b" => "689f38", "t" => "FFFFFF",], ["b" => "558b2f", "t" => "FFFFFF",], ["b" => "33691e", "t" => "FFFFFF",],
        ["b" => "f44336", "t" => "FFFFFF",], ["b" => "e91e63", "t" => "FFFFFF",], ["b" => "9c27b0", "t" => "FFFFFF",],
        ["b" => "673ab7", "t" => "FFFFFF",], ["b" => "3f51b5", "t" => "FFFFFF",], ["b" => "2196f3", "t" => "FFFFFF",],
        ["b" => "03a9f4", "t" => "FFFFFF",], ["b" => "00bcd4", "t" => "FFFFFF",], ["b" => "009688", "t" => "FFFFFF",],
    ];

    public function make(Request $request)
    {
        $this->handleInputs($request);

        $manager = new ImageManager(new Driver());
        $image = $manager->createImage($this->size, $this->size)->fill($this->background);

        $initials = $this->getInitials($this->name);

        $fontPath = public_path("assets/admin/fonts/hkgrotesk-bold.ttf");
        if (!file_exists($fontPath)) {
            $fontPath = public_path("ui_velzon_admin/assets/fonts/hkgrotesk-bold.ttf");
        }

        if (file_exists($fontPath)) {
            $image->text($initials, $this->size / 2, $this->size / 2, function ($font) use ($fontPath) {
                $font->file($fontPath);
                $font->size($this->size * $this->fontSize);
                $font->color("#" . $this->color);
                $font->align("center", "center"); // Cú pháp v4 chuẩn: cả hai đều dùng center
            });
        }

        $encoded = (string) $image->encodeUsingMediaType("image/png");

        return response($encoded)
            ->header("Content-Type", "image/png")
            ->header("Cache-Control", "public, max-age=31536000, immutable");
    }

    private function handleInputs(Request $request)
    {
        $data = $request->all();
        $this->name = !empty($data["name"]) ? $data["name"] : "User";
        $this->length = !empty($data["length"]) ? (int)$data["length"] : $this->length;
        $this->fontSize = !empty($data["fontSize"]) ? (float)$data["fontSize"] : $this->fontSize;
        $this->size = !empty($data["size"]) ? (int)$data["size"] : $this->size;
        $this->color = !empty($data["color"]) ? str_replace("#", "", $data["color"]) : $this->color;
        $this->uppercase = isset($data["uppercase"]) ? (bool)$data["uppercase"] : $this->uppercase;
        $this->rounded = isset($data["rounded"]) ? (bool)$data["rounded"] : $this->rounded;

        if (!empty($data["background"])) {
            $this->background = str_replace("#", "", $data["background"]);
        } else {
            $this->getUserBackground($request);
        }
    }

    private function getUserBackground(Request $request)
    {
        $userId = $request->get("id");
        $this->getRandomBackground();

        if (!empty($userId)) {
            $user = DB::table("users")->select("avatar_settings")->where("id", $userId)->first();
            if ($user) {
                if (!empty($user->avatar_settings)) {
                    $settings = json_decode($user->avatar_settings, true);
                    $this->color = $settings["color"] ?? "FFFFFF";
                    $this->background = $settings["background"] ?? "5e35b1";
                } else {
                    $data = ["color" => $this->color, "background" => $this->background];
                    DB::table("users")->where("id", $userId)->update(["avatar_settings" => json_encode($data)]);
                }
            }
        }
    }

    private function getRandomBackground()
    {
        $randomColor = $this->colors[rand(0, count($this->colors) - 1)];
        $this->color = $randomColor["t"];
        $this->background = $randomColor["b"];
    }

    protected function getInitials($name)
    {
        $words = explode(" ", trim($name));
        $initials = "";
        if (count($words) >= 2) {
            $initials = mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1);
        } else {
            $initials = mb_substr($name, 0, $this->length);
        }
        return $this->uppercase ? mb_strtoupper($initials) : $initials;
    }
}
