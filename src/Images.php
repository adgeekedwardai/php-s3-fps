<?php

namespace Cyntelli\Fps;

use CURLFILE;
use Exception;

/**
 * Images.
 * 
 * @author Edward Ai <edward.ai@adgeek.com.tw>
 * @since 1.0.0
 * @version 1.0.0
 */
class Images
{
    /**
     * @var string $s3repo
     */
    public $s3repo = 'https://s3fps.adgeek.net/';

    /**
     * @var string $bucketPath
     */
    public $bucketPath = "/upload/s3fpstest/my_custom";

    /**
     * @var string $targets3Path
     */
    private $targets3Path;

    /**
     * construct
     */
    public function  __construct()
    {
        $this->targets3Path = sprintf('%s%s', $this->s3repo, $this->bucketPath);
    }


    /**
     * upload Images
     * 
     * @param string $filePath
     * 
     * @return array
     */
    public function upload(string $fromPath): array
    {
        $result = [];
 
        /** init response data */
        $result['_data'] = [];

        /** tmp folder */
        $tmpPath = __DIR__.'/../runtime/';

        /** create tmp path */
        if (!is_dir($tmpPath)) {
            mkdir($tmpPath);
        }

        /** get destination */
        $destinationPath = sprintf('%s%s', $tmpPath, basename($fromPath));

        file_put_contents($destinationPath, file_get_contents($fromPath));

        /** prepare post data */
        $postData = [
            'file' => new CURLFILE($destinationPath)
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->targets3Path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
        ));

        $response = curl_exec($curl);

        $response = json_decode($response, true);

        if ($response['code'] != 200) {
            throw new Exception($response['msg'], $response['code']);
        }

        if ($response['code'] === 200) {
            $rowData = $response['data'];

            foreach ($rowData as $rowFileName => $rowFilePath) {
                array_push($result['_data'], [
                    'logo_image_path' => $rowFilePath
                ]);
            }
        }

        /** delete uploaded data */
        unlink($destinationPath);

        return $result;
    }
}