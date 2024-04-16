<?php

namespace MJ\Upload;


use stdClass;
use function MJ\Keys\sendResponse;


require_once $_SERVER['DOCUMENT_ROOT'] . "/core/KEYS.php";


/**
 * Class FileUploader
 * @package MJ\FileUploader
 */
class Upload
{
    /**
     * Next Features
     * *****************************
     * Change Allowed & Blacklist
     * Change Max Allowed Size
     *
     */


    /**
     * @var string
     */
    private static $fileName;


    /**
     * @var string
     */
    private static $fileExtension;


    /**
     * @var string | int
     */
    private static $fileSize;


    /**
     * @var string
     */
    private static $fileType;


    /**
     * @var string
     */
    private static $fileTemp;


    /**
     * @var array
     */
    private static $allowed = [
        'jpg',
        'JPG',
        'jpeg',
        'JPEG',
        'jpe',
        'JPE',
        'gif',
        'webp',
        'png',
        'PNG',
        'bmp',
        'zip',
        'pdf',
        'PDF',
        'svg',
        'webm',
        'flv',
        'mkv',
        'avi',
        'mov',
        'wmv',
        'mp4',
        'docx',
        'Docx',
        'DOCX',
        'doc',
        'Doc',
        'DOC',
        'txt',
        'xml',
        'gif',
        'GIF',
        'svg',
        'SVG',
    ];


    /**
     * @var array
     */
    private static $blacklist = [
        'php',
        'php7',
        'php6',
        'php5',
        'php4',
        'php3',
        'phtml',
        'pht',
        'phpt',
        'phtm',
        'phps',
        'inc',
        'pl',
        'py',
        'cgi',
        'asp',
        'js',
        'sh',
        'phar',
    ];


    /**
     * @var int
     */
    private static $maxAllowedSize = 10;


    /**
     * @var array
     */
    private static $resizableTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];


    /**
     * @var array
     */
    private static $supportedBase64 = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];


    /**
     *
     * @param array $file
     * @author Tjavan
     * @version 1.0.0
     */
    private static function init($file)
    {
        self::$fileName = pathinfo($file['name'], PATHINFO_FILENAME);
        self::$fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        self::$fileSize = $file['size'];
        self::$fileType = $file['type'];
        self::$fileTemp = $file['tmp_name'];
    }


    /**
     *
     * @param array $file
     * @param string $path
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function upload($file, $path = 'tmp', $fileName = null)
    {
        $response = sendResponse(0, "No file chosen for upload.");
        if (!empty($file) && isset($file['name'])) {
            if (!file_exists(getcwd() . $path) && !is_dir(getcwd() . $path)) {
                mkdir(getcwd() . $path, 0777, true);
            }
            self::init($file);
            if (self::isAllowedMaxSize(self::$fileSize)) {
                if (self::isAllowedType(self::$fileExtension)) {
                    if (!empty($fileName)) {
                        $uploadName = $fileName . "." . self::$fileExtension;
                    } else {
                        $uploadName = md5(self::$fileName . floor(microtime(true) * 1000)) . "." . self::$fileExtension;
                    }
                    $uploadLocation = SITE_ROOT . "/{$path}/";
                    $uploadDestination = "{$uploadLocation}{$uploadName}";
                    $uploadURL = "{$path}/{$uploadName}";

                    if (move_uploaded_file(self::$fileTemp, $uploadDestination)) {
                        $response = sendResponse(200, "File uploaded successfully.", $uploadURL);
                    } else {
                        $response = sendResponse(-1, "An error occurred while uploading the file.");
                    }
                } else {
                    $response = sendResponse(-2, "The type of your selected file doesn't allowed.");
                }
            } else {
                $response = sendResponse(-3, "The selected file size is higher than the maximum allowed size.");
            }
        }
        return $response;
    }


    /**
     *
     * @param array $files
     * @param string $path
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function multiple($files, $path = 'tmp')
    {
        $response = sendResponse(0, "No file chosen for upload.");

        if (!empty($files) && is_array($files) && count($files['name']) > 1) {
            if (!file_exists(getcwd() . $path) && !is_dir(getcwd() . $path)) {
                mkdir(getcwd() . $path, 0777, true);
            }

            $filesList = [];
            for ($i = 0; $i < count($files['name']); $i++) {
                array_push($filesList, [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'size' => $files['size'][$i],
                ]);
            }

            $countOfFiles = count($files['name']);
            $uploadingErrorFiles = [];
            $uploadedFilesCount = 0;
            $uploadedFilesURL = [];

            foreach ($filesList as $file) {
                self::init($file);

                if (self::isAllowedMaxSize(self::$fileSize)) {
                    if (self::isAllowedType(self::$fileExtension)) {
                        $uploadName = md5(self::$fileName . floor(microtime(true) * 1000)) . "." . self::$fileExtension;
                        $uploadLocation = SITE_ROOT . "/{$path}/";
                        $uploadDestination = "{$uploadLocation}{$uploadName}";
                        $uploadURL = SITE_URL . "/{$path}/{$uploadName}";

                        if (move_uploaded_file(self::$fileTemp, $uploadDestination)) {
                            array_push($uploadedFilesURL, $uploadURL);
                            $uploadedFilesCount++;
                        } else {
                            array_push($uploadingErrorFiles, self::$fileName . " An error occurred while uploading the file.");
                        }
                    } else {
                        array_push($uploadingErrorFiles, self::$fileName . " The type of your selected file doesn't allowed.");
                    }
                } else {
                    array_push($uploadingErrorFiles, self::$fileName . " The selected file size is higher than the maximum allowed size.");
                }
            }

            if ($countOfFiles == $uploadedFilesCount) {
                $response = sendResponse(200, "Files uploaded successfully.", $uploadedFilesURL);
            } elseif ($countOfFiles > $uploadedFilesCount && $uploadedFilesCount > 0) {
                $response = sendResponse(206, "Some files uploaded successfully.", $uploadedFilesURL, $uploadingErrorFiles);
            } else {
                $response = sendResponse(-1, "An error occurred while uploading the file.");
            }
        }

        return $response;
    }


    /**
     *
     * @param string $URI
     * @param string $path
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function uploadBase64($URI, $path = 'tmp', $fileName = null)
    {
        $response = sendResponse(0, "No file chosen for upload.");
        if (!empty($URI)) {
            if (!file_exists(getcwd() . $path) && !is_dir(getcwd() . $path)) {
                mkdir(getcwd() . $path, 0777, true);
            }


            if (self::isAllowedBase64($URI)) {
                switch (self::getBase64Type($URI)) {
                    case 'image/png':
                        $img = str_replace(' ', '+', str_replace('data:image/png;base64,', '', $URI));
                        break;

                    case 'image/jpeg':
                        $img = str_replace(' ', '+', str_replace('data:image/jpeg;base64,', '', $URI));
                        break;

                    case 'image/gif':
                        $img = str_replace(' ', '+', str_replace('data:image/gif;base64,', '', $URI));
                        break;

                    default:
                        $img = str_replace(' ', '+', str_replace('data:image/webp;base64,', '', $URI));
                        break;
                }

                $binary = base64_decode($img);

                if (!empty($fileName)) {
                    $uploadName = $fileName . ".webp";
                } else {
                    $uploadName = md5(base64_encode(floor(microtime(true) * 1000))) . ".webp";
                }
                $uploadDestination = SITE_ROOT . "/{$path}/{$uploadName}";
                $uploadURL = "{$path}/{$uploadName}";

                if (file_put_contents($uploadDestination, $binary)) {
                    $response = sendResponse(200, "File uploaded successfully.", $uploadURL);
                } else {
                    $response = sendResponse(-1, "An error occurred while uploading the file.");
                }
            } else {
                $response = sendResponse(-2, "The type of your selected file doesn't allowed.");
            }
        }

        return $response;
    }


    /**
     *
     * @param array $file
     * @param int $width
     * @param int $height
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function resizeImage($file, $width, $height)
    {
        $response = sendResponse(0, "No file chosen for resize.");
        if (!empty($file) && isset($file['name'])) {
            self::init($file);
            if (self::isResizableImage(self::$fileType)) {
                ob_start();

                $originalImageInfo = getimagesize(self::$fileTemp);
                $originalWidth = $originalImageInfo[0];
                $originalHeight = $originalImageInfo[1];

                switch (self::$fileExtension) {
                    case 'png':
                        $originalImage = imagecreatefrompng(self::$fileTemp);
                        break;

                    case 'gif':
                        $originalImage = imagecreatefromgif(self::$fileTemp);
                        break;

                    case 'webp':
                        $originalImage = imagecreatefromwbmp(self::$fileTemp);

                        break;

                    default:
                        $originalImage = imagecreatefromjpeg(self::$fileTemp);
                        break;
                }

                $newImage = imagecreatetruecolor($width, $height);
                imagecopyresampled($newImage, $originalImage,
                    0, 0,
                    0, 0,
                    $width, $height,
                    $originalWidth,
                    $originalHeight);
                imagewebp($newImage);

                $img = base64_encode(ob_get_contents());

                $output = "data:image/webp;base64,{$img}";

                imagedestroy($originalImage);
                imagedestroy($newImage);

                ob_end_clean();

                $response = sendResponse(200, "Image resized successfully.", $output);
            } else {
                $response = sendResponse(-1, "The type of your selected file can't resized.");
            }
        }
        return $response;
    }


    /**
     *
     * @param string $extension
     * @return bool
     * @author Tjavan
     * @version 1.0.0
     */
    private static function isAllowedType($extension)
    {
        if (in_array($extension, self::$allowed) && !in_array($extension, self::$blacklist)) {
            return true;
        }
        return false;
    }


    /**
     *
     * @param int $size
     * @return bool
     * @author Tjavan
     * @version 1.0.0
     */
    private static function isAllowedMaxSize($size)
    {
        if ($size <= (self::$maxAllowedSize * 1024 * 1024 * 1024)) {
            return true;
        }
        return false;
    }


    /**
     *
     * @param string $type
     * @return bool
     * @author Tjavan
     * @version 1.0.0
     */
    private static function isResizableImage($type)
    {
        if (in_array($type, self::$resizableTypes)) {
            return true;
        }
        return false;
    }


    /**
     *
     * @param string $URI
     * @return bool
     * @author Tjavan
     * @version 1.0.0
     */
    private static function isAllowedBase64($URI)
    {
        if (!empty($URI)) {
            $pos = strpos($URI, ';');
            $type = explode(':', substr($URI, 0, $pos))[1];
            if (in_array($type, self::$supportedBase64)) {
                return true;
            }
        }
        return false;
    }


    /**
     *
     * @param string $URI
     * @return string | null
     * @author Tjavan
     * @version 1.0.0
     */
    private static function getBase64Type($URI)
    {
        if (!empty($URI)) {
            $pos = strpos($URI, ';');
            $type = explode(':', substr($URI, 0, $pos))[1];
            return $type;
        }
        return null;
    }
}