<?php

class Controller
{
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = [])
    {
        extract($data);
        require_once '../app/views/' . $view . '.php';
    }

    public function response($data, $status = 200)
    {
        header("Content-Type: application/json");
        http_response_code($status);
        echo json_encode($data);
    }

    public function redirect($url)
    {
        header("Location: " . URL_PUBLIC . "/$url");
        exit;
    }

    public function redirectLogin()
    {
        header("Location: " . URL_PUBLIC. '/login');
        exit;
    }

    public function input($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    public function files($key, $default = null)
    {
        return $_FILES[$key] ?? $default;
    }

    public function session($key, $default = null)
    {
        return $_SESSION[SYSTEM][$key] ?? $default;
    }

    public function session_put($key, $default = null)
    {
        $_SESSION[SYSTEM][$key] = $default;
    }

    public function isImage($fileTmp)
    {
        return exif_imagetype($fileTmp) !== false;
    }

    public function generateUniqueFileName($fileName)
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        return uniqid() . "." . $extension;
    }


    public function uploadImage($file, $uploadDir)
    {
        $uploadedImage = "";

        try {
            $fileName = $file["name"];
            $fileTmp = $file["tmp_name"];

            // Check if file is an image
            if (!$this->isImage($fileTmp)) {
                throw new Exception("The file is not a valid image.");
            }

            $uniqueFileName = $this->generateUniqueFileName($fileName);
            $targetFile = $uploadDir . "/" . $uniqueFileName;

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($fileTmp, $targetFile)) {
                $uploadedImage = $uniqueFileName;
            } else {
                throw new Exception("Failed to upload the image.");
            }

            return [
                "status" => true,
                "image" => $uploadedImage
            ];
        } catch (Exception $e) {
            // Handle exceptions
            return ["status" => false, "error" => $e->getMessage()];
        }
    }
}
