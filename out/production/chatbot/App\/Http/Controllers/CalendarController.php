<?php
//
//namespace App\Http\Controllers;
//
//use Intervention\Image\Facades\Image;
//
//class CalendarController extends Controller
//{
//    public function createCalendarImage($year, $month)
//    {
//        // Create a blank canvas
//        $width = 800;
//        $height = 600;
//        $image = Image::canvas($width, $height, '#FFFFFF');
//
//        // Set font properties
//        $titleFontSize = 32;
//        $dayFontSize = 20;
//        $headerFontSize = 24;
//
//        // Title text
//        $title = date('F Y', strtotime("$year-$month-01"));
//        $image->text($title, $width / 2, 40, function ($font) use ($titleFontSize) {
//            $font->file(public_path('fonts/arial.ttf'));
//            $font->size($titleFontSize);
//            $font->color('#000000');
//            $font->align('center');
//        });
//
//        // Days of the week header
//        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
//        $xOffset = 50;
//        $yOffset = 100;
//        $cellWidth = ($width - 2 * $xOffset) / 7;
//        $cellHeight = 50;
//
//        foreach ($daysOfWeek as $i => $day) {
//            $image->text($day, $xOffset + $i * $cellWidth + 20, $yOffset, function ($font) use ($headerFontSize) {
//                $font->file(public_path('fonts/arial.ttf'));
//                $font->size($headerFontSize);
//                $font->color('#000000');
//            });
//        }
//
//        // Draw the days of the month
//        $date = new DateTimeImmutable("$year-$month-01");
//        $firstDay = (int) $date->format('N'); // Day of the week (1 = Mon, 7 = Sun)
//        $daysInMonth = (int) $date->format('t'); // Total days in the month
//
//        $xOffset = 50;
//        $yOffset += $cellHeight;
//        $currentX = $xOffset + ($firstDay - 1) * $cellWidth;
//        $currentY = $yOffset;
//
//        for ($day = 1; $day <= $daysInMonth; $day++) {
//            $image->text((string) $day, $currentX + 15, $currentY, function ($font) use ($dayFontSize) {
//                $font->file(public_path('fonts/arial.ttf'));
//                $font->size($dayFontSize);
//                $font->color('#000000');
//            });
//
//            // Move to the next cell
//            $currentX += $cellWidth;
//            if (($firstDay + $day - 1) % 7 == 0) {
//                $currentX = $xOffset;
//                $currentY += $cellHeight;
//            }
//        }
//
//        // Save or output the image
//        $imagePath = public_path("calendar_{$year}_{$month}.png");
//        $image->save($imagePath);
//
//        return response()->download($imagePath)->deleteFileAfterSend();
//    }
//}
