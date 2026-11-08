<?php

namespace App\Domain\Services;

class CloudStorageService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        $config = require BASE_PATH . '/Config/cloudinary.php';
        $this->cloudName = $config['cloud_name'];
        $this->apiKey = $config['api_key'];
        $this->apiSecret = $config['api_secret'];
    }

    /**
     * Upload an image to Cloudinary
     * 
     * @param string $filePath Full path to the local file
     * @param string $folder Folder in Cloudinary
     * @return string|null URL of the uploaded image or null on failure
     */
    public function upload(string $filePath, string $folder = 'room_types'): ?string
    {
        $result = $this->uploadDetails($filePath, $folder);
        return $result['secure_url'] ?? null;
    }

    /**
     * Upload and return both URL and public id for later deletion.
     *
     * @return array{secure_url: string, public_id: string}|null
     */
    public function uploadDetails(string $filePath, string $folder = 'room_types'): ?array
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $timestamp = time();
        $signatureData = "folder=$folder&timestamp=$timestamp" . $this->apiSecret;
        $signature = sha1($signatureData);

        $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";

        $data = [
            'file' => new \CURLFile($filePath),
            'timestamp' => $timestamp,
            'api_key' => $this->apiKey,
            'signature' => $signature,
            'folder' => $folder,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            return null;
        }

        $result = json_decode($response, true);
        if (empty($result['secure_url']) || empty($result['public_id'])) {
            return null;
        }

        return [
            'secure_url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function delete(string $publicId): bool
    {
        if ($publicId === '') {
            return false;
        }

        $timestamp = time();
        $signatureData = "public_id=$publicId&timestamp=$timestamp" . $this->apiSecret;
        $signature = sha1($signatureData);
        $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";

        $data = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $this->apiKey,
            'signature' => $signature,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return false;
        }

        $result = json_decode((string) $response, true);
        return in_array(($result['result'] ?? ''), ['ok', 'not found'], true);
    }
}

