<?php
include_once('config/config.php');
include_once('Db.php');
include_once('Views.php');
include_once('Files.php');
include_once('Scanner.php');

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
    //Check if project were scanned and set the message
    $project['project_scanned'] = '';
    if ($project['project_scanned'] == 0) {
      $project['project_scanned'] = '<a href="/scan/' . $project_id . '"><span class="button">Your project didn\'t scanned yet. <br><strong>Click here to perform new scan</strong><br>Get popcorn and enjoy fun.</span></a>';
    }
    //Load template
    $project_template = new Views('templates/project.tpl.php');
    foreach ($project as $key => $value) {
      $project_template->set($key, $value);
    }
    $project_template->set('header', $project_template->addHeader());
    $project_template->set('footer', $project_template->addFooter());

    return $project_template->render();
  }

  /**
   * Scan project page, here the magic begins ;-)
   * @param $project_id
   */
  public function scan($project_id){

    //Load scanner template
    $scanner = new Views('templates/scanner.tpl.php');
    //Get project info
    $project = $this->db->getProjectInfo($project_id);

    foreach ($project as $key => $value) {
     $scanner->set($key, $value);
    }
    $scanner->set('project_id',$project_id);
    $scanner->set('header', $scanner->addHeader());
    $scanner->set('footer', $scanner->addFooter());

    return $scanner->render();
  }


  public function scanAjax($project_id){
    $scanner = new Scanner($project_id);
    $scanner->initScan();
  }

}