<?php
require_once(realpath(dirname(__FILE__)).'/Db.php');
require_once(realpath(dirname(__FILE__)).'/Views.php');
require_once(realpath(dirname(__FILE__)).'/Files.php');
require_once(realpath(dirname(__FILE__)).'/Scanner.php');

Class Controller {

  private $db;

  public function __construct() {
    $this->db = new Db;
  }

  /**
   * Index action - is showed when method in controller is don't exists
   */
  public function index() {
    // Get projects list
    $projects = $this->db->getProjects();
    // Load index template
    $index_template = new Views('templates/index.tpl.php');
    // Add header and footer template
    $index_template->set('header', $index_template->addHeader());
    $index_template->set('footer', $index_template->addFooter());
    //Check if is there any projects
    if ($projects) {
      //Prepare list of projects in "index_table" template
      foreach ($projects as $id => $project_list) {
        $projects_table = new Views('templates/index_table.tpl.php');
        foreach ($project_list as $key => $value) {
          $projects_table->set($key, $value);
        }
        $project_data[$id] = $projects_table;
      }
      // Prepare index page and merge projects into one file
      $project_contest = Views::merge($project_data);
      $index_template->set('projects_table', $project_contest);
    }
    //If not show message about add new projects
    else {
      $index_template->set('projects_table', '<h3 class="no-projects">Nope, you don\'t have added any projects. You can do it by clicking "add new project" on menu link.</h3>');
    }
    return $index_template->render();
  }

  /**
   *
   * Add action - add new project form
   */
  public function add() {
    // Load add template
    $add = new Views('templates/add.tpl.php');
    // Add header and footer template
    $add->set('header', $add->addHeader());
    $add->set('footer', $add->addFooter());
    $add->set('error', '');
    $add->set('success', '');

    //Check if user posted form
    if (isset($_POST['name'])) {
      $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
      $file = new Files($_FILES['file']);
      //Manage uploaded file. If is something wrong with file return error
      if ($destination = $file->manageUploadedFile()) {
        $this->db->addProject($name, $destination);
        $add->set('success', ' <span class="button">Your project has been created, go to project list page and start your scan!</span>');
      }
      else {
        $add->set('error', ' <span class="button error_button">File error: Cannot extract file. Check archive or directory permissions.</span>');
      }
    }
    return $add->render();
  }

  /**
   * Project info page
   * @param $project_id
   * @return mixed|string
   */
  public function project($project_id) {
    //Load project info
    $project = $this->db->getProjectInfo($project_id);
    $reports = $this->db->getReportsInfo($project_id);
    //Check is project exists
    if (empty($project['project_id'])) {
      header("Location: /");
      die();
    }
    //Load template
    $project_template = new Views('templates/project.tpl.php');
    //Check if project were scanned and set the message
    $project['project_scanned_message'] = '';
    $project_template->set('project_reports', '');
    $project_template->set('header', $project_template->addHeader());
    $project_template->set('footer', $project_template->addFooter());

    switch ($project['project_scanned']) {
      //Project is not scanned yet.
      case 0:
        $project['project_scanned_message'] = '<a href="/scan/' . $project_id . '"><span class="button">Your project didn\'t scanned yet. <br><strong>Click here to perform new scan</strong><br>Get popcorn and enjoy fun.</span></a>';
        break;
      //Project scanned
      case 1:
        if (!$reports) {
          $project_template->set('project_reports', "<div class=\"infobox\"><h3>Sooo nice ;-)</h3><p>Scanner didn't found anything. So your project is sooo secure. You are security mastah, or the filters are too weak ;-) Anyway, I recommend to do a manual code review, to be 100% sure ;-)</p></div>");
        }
        else {
          // Add reports to template
          foreach ($reports as $id => $reports_list) {
            $reports_table = new Views('templates/report_box.tpl.php');
            foreach ($reports_list as $key => $value) {
              $reports_table->set($key, $value);
            }
            $reports_data[$id] = $reports_table;
          }
          // Prepare index page and merge reports into one file
          $reports_contest = Views::merge($reports_data);
          $project_template->set('project_reports', $reports_contest);
        }

        break;
      //Scan in progress
      case 2:
        $project['project_scanned_message'] = '<span class="button warning_button">Your project is scanned now <br>Please, come back later ;-)</span>';
        break;
    }

    foreach ($project as $key => $value) {
      $project_template->set($key, $value);
    }
    return $project_template->render();
  }

  /**
   * Scan project page, here the magic begins ;-)
   * @param $project_id
   */
  public function scan($project_id) {

    //Load scanner template
    $scanner = new Views('templates/scanner.tpl.php');
    //Get project info
    $project = $this->db->getProjectInfo($project_id);

    foreach ($project as $key => $value) {
      $scanner->set($key, $value);
    }
    $scanner->set('project_id', $project_id);
    $scanner->set('header', $scanner->addHeader());
    $scanner->set('footer', $scanner->addFooter());

    return $scanner->render();
  }

  /*
   * Scan Ajax page, perform scanning
   */
  public function scanAjax($project_id) {
    $scanner = new Scanner($project_id);
    $scanner->initScan();
  }


  public function report($report_file_signature) {
    $reports = $this->db->getReports($report_file_signature);
    //Check if report exists
    if (!empty($reports)) {
      $project = $this->db->getProjectInfo($reports[0]['project_id']);
      $file_name = $reports[0]['report_file'];
      $reports_template = new Views('templates/reports.tpl.php');
      $reports_template->set('header', $reports_template->addHeader());
      $reports_template->set('footer', $reports_template->addFooter());
      $reports_template->set('file_name', $file_name);
      foreach ($project as $key => $value) {
        $reports_template->set($key, $value);
      }
      foreach ($reports as $id => $reports_list) {

        $code = (array)json_decode($reports_list['report_code']);
        $reports_table = new Views('templates/report_info.tpl.php');
        $reports_table->set('report_id', $reports_list['report_id']);
        $reports_table->set('report_language', $reports_list['report_language']);
        $reports_table->set('report_type', $reports_list['report_type']);
        $reports_table->set('report_line', $reports_list['report_line']);
        $reports_table->set('report_code', htmlentities(implode($code)));
        $reports_table->set('report_first_line', key($code));
        $reports_table->set('report_ticket', $reports_list['report_ticket']);
        $reports_table->set('report_false', $reports_list['report_false'] == 0 ? 'false' : '');
        $reports_data[$id] = $reports_table;
      }

      $reports_contest = Views::merge($reports_data);
      $reports_template->set('project_reports', $reports_contest);
      print $reports_template->render();
    }
    //If not redirect to /
    header('Location: /');
    die();

  }
}

