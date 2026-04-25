<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Support\Facades\Storage;
use Exception;
use GuzzleHttp\Client as HttpClient;
use App\Helpers\SSLHelper;
use App\Helpers\GoogleHttpHandler;

class GoogleDriveService
{
    protected $client;
    protected $httpClient;
    protected $rootFolderId;
    protected $initialized = false;
    protected $accessToken;

    /**
     * Initialize Google Client
     * Lazy initialization - hanya saat dibutuhkan
     */
    private function initializeClient()
    {
        if ($this->initialized) {
            return;
        }

        try {
            // ⭐ AGGRESSIVE: Force all SSL/TLS settings BEFORE Google Client is created
            
            // 1. Set curl/openssl to use NO verification (most aggressive)
            @putenv('OPENSSL_CONF=');  // Clear OpenSSL config
            @ini_set('open_basedir', '');
            
            // 2. Force Guzzle client with explicit SSL disabled
            $handler = new \GuzzleHttp\Handler\CurlHandler();
            $stack = \GuzzleHttp\HandlerStack::create($handler);
            
            // Add middleware to force curl options
            $stack->push(function (callable $handler) {
                return function ($request, array $options) use ($handler) {
                    $options['verify'] = false;
                    $options['ssl_version'] = CURL_SSLVERSION_TLSv1_2;
                    $options['curl'] = array_merge($options['curl'] ?? [], [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    ]);
                    return $handler($request, $options);
                };
            }, 'force_ssl_disable');
            
            $guzzleClient = new \GuzzleHttp\Client([
                'handler' => $stack,
                'verify' => false,
                'http_errors' => false,
                'timeout' => 60,
            ]);
            
            // 3. Create Google Client with Guzzle
            $this->client = new \Google\Client();
            $this->client->setHttpClient($guzzleClient);
            
            // 4. Get credentials path - ONLY use google.json
            $credentialsPath = config('services.google.drive.credentials_path');
            if ($credentialsPath && !file_exists($credentialsPath)) {
                $credentialsPath = storage_path($credentialsPath);
            }
            
            if (!$credentialsPath || !file_exists($credentialsPath)) {
                throw new Exception('Google Drive credentials file not found at: ' . ($credentialsPath ?: 'not configured'));
            }
            
            $this->client->setAuthConfig($credentialsPath);
            $this->client->addScope(\Google\Service\Drive::DRIVE);
            $this->client->setApplicationName('Perjalanan Dinas System');
            
            // 5. Get access token - with SSL disabled via Guzzle
            try {
                $this->accessToken = $this->client->fetchAccessTokenWithAssertion();
            } catch (\Exception $e) {
                //Try once more if first attempt fails
                \Log::warning('First token fetch failed, retrying...', ['error' => $e->getMessage()]);
                sleep(1);
                $this->accessToken = $this->client->fetchAccessTokenWithAssertion();
            }
            
            if (isset($this->accessToken['error'])) {
                throw new Exception('Failed to get access token: ' . ($this->accessToken['error_description'] ?? $this->accessToken['error']));
            }
            
            // 6. Initialize HTTP client for REST calls
            $handlerRest = new \GuzzleHttp\Handler\CurlHandler();
            $stackRest = \GuzzleHttp\HandlerStack::create($handlerRest);
            
            $stackRest->push(function (callable $handler) {
                return function ($request, array $options) use ($handler) {
                    $options['verify'] = false;
                    $options['curl'] = array_merge($options['curl'] ?? [], [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => 0,
                    ]);
                    return $handler($request, $options);
                };
            }, 'force_ssl_disable');
            
            $this->httpClient = new HttpClient([
                'handler' => $stackRest,
                'verify' => false,
                'http_errors' => false,
                'timeout' => 60,
            ]);
            
            $this->rootFolderId = config('services.google.drive.root_folder_id');
            if (!$this->rootFolderId) {
                throw new Exception('GOOGLE_DRIVE_ROOT_FOLDER_ID not configured');
            }
            
            $this->initialized = true;
            
            \Log::info('Google Drive initialized successfully', [
                'root_folder_id' => substr($this->rootFolderId, 0, 10) . '...',
            ]);
            
        } catch (Exception $e) {
            \Log::error('Google Drive initialization failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
    
    /**
     * Upload file ke Google Drive
     * Struktur: Root Folder > Nomor SPT
     */
    public function createSPTFolder($nomorSPT)
    {
        try {
            $this->initializeClient();
            
            $folderName = "SPT_" . str_replace(['/', '\\', ' '], '_', $nomorSPT);
            
            $body = [
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$this->rootFolderId],
            ];
            
            $response = $this->httpClient->post('https://www.googleapis.com/drive/v3/files', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);
            
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            if ($statusCode !== 200) {
                \Log::error('Google Drive folder creation failed', [
                    'status' => $statusCode,
                    'response' => $responseBody,
                ]);
                throw new Exception('Failed to create folder. Status: ' . $statusCode . '. Response: ' . $responseBody);
            }
            
            $folder = json_decode($responseBody, true);
            
            return [
                'folder_id' => $folder['id'],
                'folder_name' => $folder['name'],
                'web_link' => $folder['webViewLink'] ?? 'https://drive.google.com/drive/folders/' . $folder['id'],
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to create folder: ' . $e->getMessage());
        }
    }

    /**
     * Check apakah folder sudah ada untuk SPT
     */
    public function checkFolderExists($nomorSPT)
    {
        try {
            $this->initializeClient();
            
            $folderName = "SPT_" . str_replace(['/', '\\', ' '], '_', $nomorSPT);
            
            $query = sprintf(
                "name = '%s' and mimeType = 'application/vnd.google-apps.folder' and '%s' in parents and trashed = false",
                addslashes($folderName),
                $this->rootFolderId
            );
            
            $response = $this->httpClient->get('https://www.googleapis.com/drive/v3/files', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken['access_token'],
                ],
                'query' => [
                    'q' => $query,
                    'spaces' => 'drive',
                    'fields' => 'files(id, name)',
                    'pageSize' => 1,
                ],
            ]);
            
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            if ($statusCode !== 200) {
                \Log::error('Google Drive folder check failed', [
                    'status' => $statusCode,
                    'response' => $responseBody,
                ]);
                throw new Exception('Failed to check folder. Status: ' . $statusCode);
            }
            
            $result = json_decode($responseBody, true);
            $files = $result['files'] ?? [];
            
            if (count($files) > 0) {
                return [
                    'exists' => true,
                    'folder_id' => $files[0]['id'],
                    'folder_name' => $files[0]['name'],
                ];
            }

            return ['exists' => false];
        } catch (Exception $e) {
            throw new Exception('Failed to check folder: ' . $e->getMessage());
        }
    }

    /**
     * Upload file ke Google Drive di folder SPT tertentu
     */
    public function uploadFile($filePath, $fileName, $folderId, $fileType)
    {
        try {
            if (!file_exists($filePath)) {
                throw new Exception("File not found: {$filePath}");
            }

            $this->initializeClient();

            // Rename dengan prefix tipe file
            $newFileName = $fileType . "_" . $fileName;

            $mimeType = $this->getMimeType($filePath);
            $fileContent = file_get_contents($filePath);

            \Log::info('Uploading to Google Drive', [
                'folder_id' => $folderId,
                'file_name' => $newFileName,
                'mime_type' => $mimeType,
                'size' => filesize($filePath),
            ]);

            // Create file metadata
            $boundary = '===============7330845974216740156==';
            $eol = "\r\n";
            $body = '';
            
            // Add metadata part
            $body .= '--' . $boundary . $eol;
            $body .= 'Content-Type: application/json; charset=UTF-8' . $eol . $eol;
            $body .= json_encode([
                'name' => $newFileName,
                'parents' => [$folderId],
                'properties' => ['file_type' => $fileType],
            ]) . $eol;
            
            // Add file content part
            $body .= '--' . $boundary . $eol;
            $body .= 'Content-Type: ' . $mimeType . $eol . $eol;
            $body .= $fileContent . $eol;
            $body .= '--' . $boundary . '--';

            $response = $this->httpClient->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken['access_token'],
                    'Content-Type' => 'multipart/related; boundary=' . $boundary,
                ],
                'body' => $body,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            if ($statusCode !== 200) {
                \Log::error('Google Drive upload failed', [
                    'status' => $statusCode,
                    'response' => $responseBody,
                ]);
                throw new Exception('Failed to upload file. Status: ' . $statusCode);
            }
            
            $file = json_decode($responseBody, true);

            \Log::info('Google Drive upload successful', [
                'file_id' => $file['id'],
                'file_name' => $file['name'],
            ]);

            return [
                'file_id' => $file['id'],
                'file_name' => $file['name'],
                'web_link' => $file['webViewLink'] ?? 'https://drive.google.com/file/d/' . $file['id'],
                'mime_type' => $file['mimeType'],
                'created_time' => $file['createdTime'] ?? null,
            ];
        } catch (Exception $e) {
            \Log::error('Google Drive upload error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new Exception('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Delete file dari Google Drive
     */
    public function deleteFile($fileId)
    {
        try {
            $this->initializeClient();
            
            $response = $this->httpClient->delete('https://www.googleapis.com/drive/v3/files/' . $fileId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken['access_token'],
                ],
            ]);
            
            $statusCode = $response->getStatusCode();
            
            if ($statusCode !== 204 && $statusCode !== 200) {
                throw new Exception('Failed to delete file. Status: ' . $statusCode);
            }
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Get MIME type berdasarkan file extension
     */
    private function getMimeType($filePath)
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
        ];

        return $mimeTypes[$ext] ?? 'application/octet-stream';
    }

    /**
     * Get file download link
     */
    public function getDownloadLink($fileId)
    {
        return "https://drive.google.com/uc?export=download&id={$fileId}";
    }

    /**
     * Get file info
     */
    public function getFileInfo($fileId)
    {
        try {
            $this->initializeClient();
            
            $response = $this->httpClient->get('https://www.googleapis.com/drive/v3/files/' . $fileId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken['access_token'],
                ],
                'query' => [
                    'fields' => 'id, name, webViewLink, mimeType, createdTime, size',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            if ($statusCode !== 200) {
                throw new Exception('Failed to get file info. Status: ' . $statusCode);
            }
            
            $file = json_decode($responseBody, true);
            
            return [
                'file_id' => $file['id'],
                'file_name' => $file['name'],
                'web_link' => $file['webViewLink'],
                'mime_type' => $file['mimeType'],
                'created_time' => $file['createdTime'],
                'size' => $file['size'] ?? null,
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to get file info: ' . $e->getMessage());
        }
    }
}