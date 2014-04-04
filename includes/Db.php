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

  public function getProjectInfo($project_id){
    try{
      $query = $this->db->prepare('SELECT p.project_id, p.project_name, p.project_timestamp, p.project_scanned FROM projects p
      WHERE p.project_id =:project_id');
      $query->bindParam(':project_id',$project_id,PDO::PARAM_INT);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_ASSOC);
      return $result;
    }
    catch(PDOException $e){
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
      $query = $this->db->prepare('INSERT INTO projects (`project_name`, `project_directory`) VALUES(:project_name, :project_directory)');
      $query->bindParam(':project_name', $name, PDO::PARAM_STR);
      $query->bindParam(':project_directory', $directory, PDO::PARAM_STR);
      $query->execute();
      return $this->db->lastInsertId();
    }
    catch (PDOException $e) {
      return FALSE;
    }
  }

}