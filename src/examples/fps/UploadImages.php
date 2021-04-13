<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Cyntelli\Fps\Images;


$result = (new Images)->upload('https://s3fps.adgeek.net/images/s3fpstest/o3Vxkzw8jRbqzBTBf-APmOayG9dBHWhrfcgVlSAhqZxsF24FpPBAkVDx0VPRGtd-dQQKE3E.jpg');


var_dump($result);