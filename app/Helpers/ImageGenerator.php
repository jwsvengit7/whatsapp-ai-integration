<?php
namespace App\Helpers;
class ImageGenerator {

    function createStyledCalendarImage($year, $month, $predictions): string
    {
        $width = 800;
        $height = 600;
        $image = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $frameColor = imagecolorallocate($image, 200, 200, 200);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $headerColor = imagecolorallocate($image, 70, 130, 180);
        $predictionColor = imagecolorallocate($image, 255, 69, 0);
        imagefill($image, 0, 0, $bgColor);

        imagerectangle($image, 5, 5, $width - 5, $height - 5, $frameColor);
        $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
        imagestring($image, 5, 20, 50, "$monthName $year", $headerColor);
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $x = 20;
        foreach ($days as $day) {
            imagestring($image, 4, $x, 100, $day, $textColor);
            $x += 100;
        }

        $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDay = date('w', mktime(0, 0, 0, $month, 1, $year));
        $x = 20;
        $y = 120;
        for ($day = 1; $day <= $numDays; $day++) {
            if ($day == 1) {
                $x += $firstDay * 100;
            }
            imagestring($image, 4, $x, $y, $day, $textColor);
            $x += 100;

            if (($day . $firstDay) % 7 == 0) {
                $x = 20;
                $y += 40;
            }
        }
        $predictionY = $y + 40;
        imagestring($image, 4, 20, $predictionY, "Predictions:", $predictionColor);
        foreach ($predictions as $index => $prediction) {
            imagestring($image, 3, 40, $predictionY + 30 + ($index * 30), "- $prediction", $textColor);
        }
        $imagePath = "/Users/user/Desktop/jack/whatsapp-ai-integration/public/images/calendar_.$year.$month.png";
        imagepng($image, $imagePath);
        imagedestroy($image);
        return $imagePath;
    }


}
