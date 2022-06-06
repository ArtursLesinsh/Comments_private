<?php
namespace Helpers;

use Storage\DB;

class Images
{
    private $db_images;
    public function __construct() {
        $this->db_images = new DB('images');
    }

    public function getAll() {
        $result = $this->db_images->getAll();

        if ($result === false) {
            return[
                'status' => false,
                'error_msg' => $this->db_images->getError()
            ];

        }
        return [
            'status' => true,
            'images' => $result
        ];    
    }

    public function upload():array {
        if (
            !isset($_POST['author']) || !is_string($_POST['author']) ||
            empty($_FILES) || !isset($_FILES['upload_image'])
        ) {
            return ['status' => true, 'message' => 'wrong data suplied'];
        }
        $image_arr = $_FILES['upload_image'];
        if ($image_arr['error'] != 0) {
            return ['status' => true, 'message' => 'wrong data suplied'];
        }

        $id = $this->addToDB($_POST['author'], $image_arr['name']);
        if ($id == false) {
            return [
                'status' => false,
                'error_msg' => $this->db_images->getError()
                ]; 
        }

        $this->saveFile($image_arr['tmp_name'], $id);
    
        return [
            'status' => true,
            'file_name' => $image_arr['name'],
            'id' => $id
        ];   
    }

    private function addToDB(string $author, string $image_name) {
        return $this->db_images->addEntry([
            'author' => trim($author),
            'file_name' => explode(',', $image_name)[0]
        ]);
    }

    private function saveFile(string $tmp_name, int $id) {
        $file_content = file_get_contents($tmp_name);
        file_put_contents(UPLOAD_DIR . "image_$id.png", $file_content);
    }
}