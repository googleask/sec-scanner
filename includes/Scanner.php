<?php
include_once(realpath(dirname(__FILE__)).'/../config/config.php');
include_once(realpath(dirname(__FILE__)).'/Db.php');
include_once(realpath(dirname(__FILE__)).'/Files.php');

Class Scanner {

  private $project_id;
  private $rules;
  private $db;
  private $files;

  public function __construct($project_id) {
    $this->project_id = $project_id;
    $this->db = new Db;
    $this->files = new Files;
  }

  /**
   * Init scan process.
   * Solution for realtime output find on: http://stackoverflow.com/questions/1281140/run-process-with-realtime-output-in-php
   * Maybe ugly, but sometimes at 3AM it's only what is getting out of head ;-)
   */
  public function initScan() {
    $view = new Views('templates/head.tpl.php');
    $view->set('class', 'scanner');
    print $view->render();
    set_time_limit(0);
    $handle = popen(PHP." scanner.php " . $this->project_id, "r");
    if (ob_get_level() == 0) {
      ob_start();
    }
    while (!feof($handle)) {
      $buffer = fgets($handle);
      $buffer = trim(htmlspecialchars($buffer));
      $data = explode(';', $buffer);
      switch ($data[0]) {
        case 'FOUND':
          print "<div class=\"infobox\"><h3>Found something</h3><p><strong>Time:</strong> " . $data[1] . "<br><strong>Filter name:</strong> " . $data[2] . "<br><strong>Line:</strong> " . $data[3] . "<br><strong>File:</strong> " . $data[4] . "</p><a href=\"/report/" . $data[5] . "\" target=\"_blank\"><span class=\"button warning_button\" style=\"\">Show report</span></a></div>";
          break;
        case 'NOT_FOUND':
          print "<div class=\"infobox\"><h3>WOW!</h3><p>Scanner didn't found anything. So your project is sooo secure. You are security mastah, or the filters are too weak ;-) Anyway, I recommend to do a manual code review, to be 100% sure ;-)</p></div>";
          break;
        case 'SCANNED':
          print "<div class=\"infobox\"><h3>Hmmmm...</h3><p>Your project has been scanned before. Please go to project to check your reports. <br><a href=\"/show/" . $this->project_id . "\" target=\"_parent\"><span class=\"button\">Go to project page</span></a></p></div>";
          break;
      }
      ob_flush();
      flush();
      time_nanosleep(0, 10000000);
    }
    pclose($handle);
    ob_end_flush();
  }


  /**
   * Perform scanner in system console
   */
  public function performScan() {
    //Load rules
    $this->loadRules();
    //Load project
    $project = $this->db->getProjectInfo($this->project_id);
    //Load files from project directory
    $directory = DESTINATION . '/' . $project['project_directory'];
    $files = $this->files->getAllFilesFromDirectory($directory);
    //Start scan
    $this->scan($files);


  }

  /**
   * Main SCAN method. This method is opening file and line by line try to match regex. If find something this method print WARNING and create report in database
   * @param $files
   */
  private function scan($files) {
    //Check if project were scanned before
    if ($this->db->getPtojectStatus($this->project_id) == 1) {
      print "SCANNED;";
      die();
    }
    //Set project status to in progress
    $this->db->setProjectStatus($this->project_id, 2);
    //Get rules
    $filters = $this->rules;
    //Found variable is temp variable for check is scanner found something. If not show message: not found
    $found = 0;
    //Main file loop
    foreach ($files as $file) {
      unset($content);
      //Check filesize. Too big file make a big problem ;-0
      if (filesize($file) < 104857600) {
        //Open file
        $content = file($file);
        //Create file signature
        $file_signature = $this->generateFileSignature($file);
        //Filename for database
        $file_name = str_replace(DESTINATION,'',$file);
        //Loop file line by line
        foreach ($content as $lineNumber => $line) {
          //Loop this line by regex filters
          foreach ($filters as $filter) {
            if (preg_match($filter['regex'], $line)) {
              $found = 1;
              //Print info about potential vuln
              print "FOUND;" . date('Y-m-d H:i:s') . ";" . $filter['name'] . ";" . $lineNumber . ";" . $file_name . ";" . $file_signature . "\n";
              for ($i = ($lineNumber - 5); $i < ($lineNumber + 5); $i++) {
                if (!empty($content[$i])) {
                  $code[$i] = $content[$i];
                }
              }
              // Get extension of file to inform syntax highlighter
              $extension = $this->files->getExtension($file);
              //Create a report id
              $report_id = $this->generateReportSignature($this->project_id, $file_name, $filter['name'], $lineNumber);
              //Add report
              $this->db->addReport($this->project_id, $report_id, $file_name, $extension, $filter['name'], $lineNumber, json_encode($code), $file_signature);
              unset($code);
            }
          }
        }
      }
    }
    if ($found == 0) {
      print "NOT_FOUND;";
    }
    //Set project status to finished
    $this->db->setProjectStatus($this->project_id, 1);

  }


  /**
   * Load rules for scan code.
   */
  private function loadRules() {
    $xml = simplexml_load_file(RULES);
    $i = 0;
    foreach ($xml as $rule) {
      $this->rules[$i]['name'] = trim((string) $rule->name);
      $this->rules[$i]['regex'] = trim((string) $rule->regex);
      $i++;
    }
  }


  /**
   * Generate report signature. It is used as report_id in database
   * @param $project_id
   * @param $report_file
   * @param $report_type
   * @param $report_line
   * @return string
   */
  private function generateReportSignature($project_id, $report_file, $report_type, $report_line) {
    return md5($project_id . $report_file . $report_type . $report_line);
  }


  private function generateFilesignature($file) {
    return md5($file);
  }


}