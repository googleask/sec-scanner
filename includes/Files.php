<?php

Class Files {

  // File upload object
  protected $file = array();

  //Allowed extensions
  private $extensions = array('zip', 'tar', 'gz', 'rar', 'tar.gz');

  //Allowed mime-types
  private $mime = array(
    'application/x-rar',
    'application/zip',
    'application/x-tar',
    'application/x-tgz',
    'application/x-gzip',
    'application/gzip',
  );


  public function __construct($file = array()) {
    $this->file = $file;
  }



  /**
   * Get all files from directory and set it on $files variable
   * @param $directory
   * @return array
   */
  public function getAllFilesFromDirectory($directory){
    $result = array();
    if (is_dir($directory)) {
      $root = scandir($directory);
      foreach ($root as $value) {
        if ($value === '.' || $value === '..' || $value === '.svn') {
          continue;
        }
        if (is_file("$directory/$value")) {
          $result[] = "$directory/$value";
          continue;
        }
        foreach ($this->getAllFilesFromDirectory("$directory/$value") as $value) {
          // Check if file is in mime type list
          if ($this->validateFile($value)) {
            $result[] = $value;
          }

        }
      }
    }
    return $result;
  }

  /**
   * Main method to manage uploaded file
   * @return bool|destination
   */
  public function manageUploadedFile() {
    if ($this->validateExtension() and $this->validateMimetype()) {
      switch ($this->file['type']) {
        case 'application/x-tar':
        case 'application/x-tgz':
        case 'application/x-gzip':
        case 'application/gzip':
          $destination = $this->untar();
          break;
        case 'application/zip':
          $destination = $this->unzip();
          break;
        case 'application/x-rar':
          $destination = $this->unrar();
          break;
      }
      return $destination;
    }
    return FALSE;
  }

  /*
   * Get extension of  file
   */
  public function getExtension($file) {
    return pathinfo($file, PATHINFO_EXTENSION);
  }

  /**
   * Unzip file to destination folder
   * @return destination folder
   */
  private function unzip() {
    $zip = new ZipArchive;
    if ($zip->open($this->file['tmp_name']) === TRUE) {
      $zip->extractTo(DESTINATION . '/' . $this->file['name']);
      $zip->close();
      //Remove temporary file
      unlink($this->file['tmp_name']);
      return $this->file['name'];
    }
    return FALSE;
  }

  /**
   * Untar file to destination folder
   * @return destination folder
   */
  private function untar() {
    $file = '/tmp/' . $this->file['name'];
    try {
      //It have to be copied into /tmp directory because Phar doesn't recognize non-tar filenames
      move_uploaded_file($this->file['tmp_name'], $file);
      $tar = new PharData($file);
      $tar->extractTo(DESTINATION . '/' . $this->file['name'],null,true);

      //Remove temporary files
      unlink($file);
      unlink($this->file['tmp_name']);
      return $this->file['name'];
    }
    catch (Exception $e) {
      return FALSE;
    }
  }


  /**
   * Unrar file to destination folder
   * @return destination folder
   */
  private function unrar() {

    try {
      $rar = rar_open($this->file['tmp_name']);
      $list = rar_list($rar);
      foreach ($list as $file) {
        $file->extract(DESTINATION . '/' . $this->file['name'] . '/');
      }
      rar_close($rar);
      unlink($this->file['tmp_name']);
      return $this->file['name'];
    }
    catch (Exception $e) {
      return FALSE;
    }
  }



  /*
   * Validate is mimetype of file is in allowed list
   */
  private function validateMimetype() {
    if (in_array($this->file['type'], $this->mime)) {
      return TRUE;
    }
    return FALSE;
  }

  /*
   * Validate is extension of file is in allowed list
   */
  private function validateExtension() {
    if (in_array($this->getExtension($this->file['name']), $this->extensions)) {
      return TRUE;
    }
    return FALSE;
  }



/**
 * Get extension of file and check if file is in mime type list
 * return TRUE - file is in list
 * return FALSE - fle is not in list
 * @param $file
 */
private function validateFile($file) {
  $extension = pathinfo($file, PATHINFO_EXTENSION);
  $list = json_decode(FILE_TYPES);
  if (in_array($extension,$list)) {
    return TRUE;
  }
  return FALSE;
}

}