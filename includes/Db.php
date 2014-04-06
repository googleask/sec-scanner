<?php

Class Db {

  private $db;


  public function __construct() {
    try {
      $this->db = new PDO('mysql:host=' . MYSQL_SERVER . ';dbname=' . MYSQL_DATABASE . ';port=' . MYSQL_PORT, MYSQL_USER, MYSQL_PASSWORD);
    }
    catch (PDOException $e) {
      print 'Unable to connect database<br />';
      print $e->getMessage();
    }
  }

  /**
   * Get all projects for Index action
   * @return mixed
   */
  public function getProjects() {
    try {
      $query = $this->db->prepare('SELECT p.project_id, p.project_name, p.project_timestamp, count(r.report_id) as reports
      FROM projects p
      LEFT JOIN reports r ON p.project_id = r.project_id
      GROUP BY p.project_id
      ORDER BY p.project_id DESC');
      $query->execute();
      $results = $query->fetchAll(PDO::FETCH_ASSOC);
      return $results;
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

  /**
   * Get info about project
   * @param $project_id
   * @return bool|mixed
   */
  public function getProjectInfo($project_id) {
    try {
      $query = $this->db->prepare('SELECT p.project_id, p.project_name, p.project_timestamp, p.project_scanned, p.project_directory, count(r.report_id) as reports FROM projects p
      LEFT JOIN reports r ON p.project_id = r.project_id
      WHERE p.project_id =:project_id');
      $query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_ASSOC);
      return $result;
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

  public function getReportsInfo($project_id) {
    try {
      $query = $this->db->prepare('SELECT report_file, report_file_signature, count(report_id) as reports_count FROM reports WHERE project_id =:project_id GROUP BY report_file');
      $query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
      $query->execute();
      $result = $query->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

  /**
   * Set status of the project
   * 0 - project not scanned
   * 1 - project scanned
   * 2 - project in progress
   * @param $project_id
   * @param $status
   */
  public function setProjectStatus($project_id, $status) {
    try {
      $query = $this->db->prepare('UPDATE projects SET project_scanned =:status WHERE project_id =:project_id');
      $query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
      $query->bindParam(':status', $status, PDO::PARAM_INT);
      $query->execute();
      return TRUE;
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

  public function getPtojectStatus($project_id) {
    try {
      $query = $this->db->prepare('SELECT project_scanned FROM projects WHERE project_id =:project_id');
      $query->bindParam(":project_id", $project_id, PDO::PARAM_STR);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_ASSOC);
      return $result['project_scanned'];
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

  /**
   * Add new project
   * @param $name
   * @param $directory
   * @return bool|string
   */
  public function addProject($name, $directory) {
    try {
      $query = $this->db->prepare('INSERT INTO projects (project_name, project_directory) VALUES(:project_name, :project_directory)');
      $query->bindParam(':project_name', $name, PDO::PARAM_STR);
      $query->bindParam(':project_directory', $directory, PDO::PARAM_STR);
      $query->execute();
      return $this->db->lastInsertId();
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

  public function addReport($project_id, $report_id, $report_file, $report_language, $report_type, $report_line, $report_code, $file_signature) {
    try {
      $query = $this->db->prepare('INSERT INTO reports
      (project_id, report_id, report_file, report_language, report_type, report_line, report_code,report_file_signature)
      VALUES(:project_id, :report_id, :report_file, :report_language, :report_type, :report_line, :report_code, :report_file_signature)');
      $query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
      $query->bindParam(':report_id', $report_id, PDO::PARAM_INT);
      $query->bindParam(':report_file', $report_file, PDO::PARAM_STR);
      $query->bindParam(':report_language', $report_language, PDO::PARAM_STR);
      $query->bindParam(':report_type', $report_type, PDO::PARAM_STR);
      $query->bindParam(':report_line', $report_line, PDO::PARAM_INT);
      $query->bindParam(':report_code', $report_code, PDO::PARAM_STR);
      $query->bindParam(':report_file_signature', $file_signature, PDO::PARAM_STR);
      $query->execute();
      return TRUE;
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

  public function getReports($report_file_signature){
    try{
      $query = $this->db->prepare("SELECT report_id, report_file, project_id,  report_language, report_type, report_line, report_code, report_false, report_ticket
      FROM reports
      WHERE report_file_signature =:report_file_signature");
      $query->bindParam(':report_file_signature',$report_file_signature,PDO::PARAM_STR);
      $query->execute();
      $result = $query->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

}