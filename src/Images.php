<?php

namespace Cyntelli\Fps;

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
     * upload Images
     * 
     * @param array $data
     * 
     * @return array
     */
    public function upload(string $data): array
    {
        $result = [];

        /** validate files data */
        $data = json_decode($data, true);

        if (!array_key_exists('bucket_path', $data)) {
            throw new Exception("Bucket path is required!", 400);
        }

        $targets3Path = 'https://s3fps.adgeek.net/'.$data['bucket_path'];
 
        /** init response data */
        $result['_data'] = [];
        $result['_msg'] = [];

        $files = $data['files'];
        foreach ($files as $rowFile) {
            /** prepare post data */
            $postData = [
                'file_base64' => $rowFile['encode_data']
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $targets3Path,
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

            if ($response['code'] === 200) {
                $rowData = $response['data'];

                foreach ($rowData as $rowFileName => $rowFilePath) {
                    array_push($result['_data'], [
                        'file_name' => $rowFileName,
                        'file_path' => $rowFilePath
                    ]);
                }
            } else {
                array_push($result['_msg'], $response);
            }
        }

        return $result;
    }
}