<?php
namespace App\Helpers;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
class ImageGenerator {





    public function createStyledCalendarImage(string $date, string $time, array $styleOptions = []): string
    {

//        $manager = new ImageManager(['driver' => 'gd'],"");
//
//        $img = $manager->canvas(400, 400, '#f2f2f2');
//        $fontSize = $styleOptions['font_size'] ?? 24;
//        $fontColor = $styleOptions['font_color'] ?? '#000000';
//        $positionX = $styleOptions['position_x'] ?? 150;
//        $positionY = $styleOptions['position_y'] ?? 200;
//
//        $img->text("Scheduled Date:", $positionX, $positionY - 20, function ($font) use ($fontSize, $fontColor) {
//            $font->file(public_path('fonts/Roboto-Bold.ttf'));
//            $font->size($fontSize);
//            $font->color($fontColor);
//            $font->align('center');
//        });
//
//        $img->text($date, $positionX, $positionY, function ($font) use ($fontSize, $fontColor) {
//            $font->file(public_path('fonts/Roboto-Bold.ttf'));
//            $font->size($fontSize);
//            $font->color($fontColor);
//            $font->align('center');
//        });
//
//        $img->text("Time: $time", $positionX, $positionY + 30, function ($font) use ($fontSize, $fontColor) {
//            $font->file(public_path('fonts/Roboto-Bold.ttf'));
//            $font->size($fontSize);
//            $font->color($fontColor);
//            $font->align('center');
//        });
//
//        // Save the image to storage or a temporary path
//        $path = public_path("calendar_images/calendar_" . time() . ".png");
//        $img->save($path);

        return "path";
    }

}
