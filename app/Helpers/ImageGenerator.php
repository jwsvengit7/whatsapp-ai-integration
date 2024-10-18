<?php
namespace App\Helpers;
class ImageGenerator {

    function createStyledCalendarImage($year, $month, $predictions): string
    {
        // Create a blank image with specified dimensions
        $width = 800;
        $height = 600;
        $image = imagecreatetruecolor($width, $height);

        // Define colors
        $bgColor = imagecolorallocate($image, 255, 255, 255); // White background
        $frameColor = imagecolorallocate($image, 200, 200, 200); // Light grey frame
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text
        $headerColor = imagecolorallocate($image, 70, 130, 180); // Steel blue for header
        $predictionColor = imagecolorallocate($image, 255, 69, 0); // Tomato for predictions

        // Fill the background
        imagefill($image, 0, 0, $bgColor);

        // Draw a frame
        imagerectangle($image, 5, 5, $width - 5, $height - 5, $frameColor);

        // Draw the month and year in a styled header
        $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
        imagestring($image, 5, 20, 50, "$monthName $year", $headerColor); // Using built-in font

        // Draw the day headers
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $x = 20;
        foreach ($days as $day) {
            imagestring($image, 4, $x, 100, $day, $textColor); // Using built-in font
            $x += 100; // Move to the next column
        }

        // Calculate the number of days in the month
        $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDay = date('w', mktime(0, 0, 0, $month, 1, $year)); // Day of the week for the first day

        // Draw the calendar days
        $x = 20;
        $y = 120;
        for ($day = 1; $day <= $numDays; $day++) {
            // Move to the correct position
            if ($day == 1) {
                $x += $firstDay * 100; // Offset for the first day
            }
            imagestring($image, 4, $x, $y, $day, $textColor); // Using built-in font
            $x += 100; // Move to the next column

            // Move to the next row if necessary
            if (($day . $firstDay) % 7 == 0) {
                $x = 20; // Reset x
                $y += 40; // Move down to the next row
            }
        }

        // Add predictions below the calendar
        $predictionY = $y + 40; // Position below the calendar
        imagestring($image, 4, 20, $predictionY, "Predictions:", $predictionColor); // Using built-in font
        foreach ($predictions as $index => $prediction) {
            imagestring($image, 3, 40, $predictionY + 30 + ($index * 30), "- $prediction", $textColor); // Using built-in font
        }

        $imagePath = "/Users/user/Desktop/jack/whatsapp-ai-integration/public/images/calendar_.$year.$month.png";
        imagepng($image, $imagePath);
        imagedestroy($image); // Free up memory

        return $imagePath; // Return the path to the image
    }


}
