<?php

Class Views {

  //File name variable
  protected $file;

  //Values variable
  protected $values = array();

  /**
   * Get file name
   * @param $file
   */
  public function __construct($file) {
    $this->file = $file;

  }

  /*
   * Set values
   */
  public function set($key, $value) {
    $this->values[$key] = $value;
  }


  //Add global header template
  public function addHeader() {
    $content = new Views('templates/header.tpl.php');
    return $content->render();
  }

//Add global header template
  public function addFooter() {
    $content = new Views('templates/footer.tpl.php');
    return $content->render();
  }

  public function render() {
    /**
     * Try to check if file exists
     */
    if (!file_exists($this->file)) {
      return "Error loading file ($this->file).<br />";
    }
    $content = file_get_contents($this->file);

    foreach ($this->values as $key => $value) {
      $replace = "[@$key]";
      $content = str_replace($replace, $value, $content);
    }

    return $content;
  }

  /**
   * Merge templates into one view
   * @param $templates
   * @param string $separator
   * @return string
   */
  static public function merge($templates, $separator = "\n") {
    $output = "";

    foreach ($templates as $template) {
      $content = (get_class($template) !== "Views")
        ? "Error, incorrect type - expected Views."
        : $template->render();

      $output .= $content . $separator;
    }

    return $output;
  }

}