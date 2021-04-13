<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Cyntelli\Fps\Images;

$images = [
    "bucket_path" => "/upload/s3fpstest/my_custom",
];

/** scan image file */
$imagePath = __DIR__.'/images/';
$files = scandir($imagePath);
$images['files'] = [];

foreach ($files as $rowFileName) {
    if (!in_array($rowFileName, array(".",".."))) {
        /** create base64encode string */
        $filePath = $imagePath.$rowFileName;
        $type = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = file_get_contents($filePath);
        $encodedData = base64_encode($data);

        list($width, $height, $type, $attr) = getimagesize("img/flag.jpg");

        array_push($images['files'], [
            'encode_data' => $encodedData
        ]);
    }
}

$result = (new Images)->upload(json_encode($images));

var_dump($result);