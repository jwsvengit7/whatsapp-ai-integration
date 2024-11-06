<?php
namespace App\Helpers;

use DateTime;

class ImageGenerator {

    public function createCalendarImage($year, $month, $width = 800, $height = 600): string {
        // Create a blank image
        $image = imagecreatetruecolor($width, $height);

        // Define colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 0, 0, 255);
        imagefill($image, 0, 0, $white);
        $titleFontSize = 5;
        $dayFontSize = 3;
        $headerFontSize = 4;
        $title = date('F Y', strtotime("$year-$month-01"));
        $titleX = ($width - imagefontwidth($titleFontSize) * strlen($title)) / 2; // Center title
        imagestring($image, $titleFontSize, $titleX, 20, $title, $black);
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $xOffset = 10;
        $yOffset = 60;
        $cellWidth = ($width - 2 * $xOffset) / 7;
        $cellHeight = 50;

        foreach ($daysOfWeek as $i => $day) {
            $dayX = $xOffset + $i * $cellWidth + 10;
            imagestring($image, $headerFontSize, $dayX, $yOffset, $day, $blue);
        }

        // Generate the month layout
        $date = new DateTime("$year-$month-01");
        $firstDay = (int) $date->format('N');
        $daysInMonth = (int) $date->format('t');

        $yOffset += $cellHeight; // Start drawing days below the header
        $currentX = $xOffset + ($firstDay - 1) * $cellWidth;
        $currentY = $yOffset;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayText = str_pad($day, 2, '0', STR_PAD_LEFT); // Pad single digits for better alignment
            imagestring($image, $dayFontSize, $currentX + 15, $currentY, $dayText, $black);

            // Move to the next cell
            $currentX += $cellWidth;
            if (($firstDay + $day - 1) % 7 == 0) { // New row every 7 days
                $currentX = $xOffset;
                $currentY += $cellHeight;
            }
        }

        // Define a file path to save the image temporarily
        $filePath = storage_path('app/public/calendar_' . $year . '_' . $month . '.png');

        // Save the image to the specified path
        imagepng($image, $filePath);

        // Free memory
        imagedestroy($image);

        // Return the file path
        return $filePath;
    }
}

