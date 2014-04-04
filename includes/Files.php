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

  public function __construct($file) {
    $this->file = $file;
  }


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
      $tar->extractTo(DESTINATION . '/' . $this->file['name'] . '/');

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
   * Get extension of uploaded file
   */
  private function getExtension() {
    return pathinfo($this->file['name'], PATHINFO_EXTENSION);
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
    if (in_array($this->getExtension(), $this->extensions)) {
      return TRUE;
    }
    return FALSE;
  }

}