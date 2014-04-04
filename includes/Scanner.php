<?php

Class Scanner {

  private $project_id;

  public function __construct($project_id) {
    $this->project_id = $project_id;
  }

  /**
   * Init scan process.
   * Solution for realtime output find on: http://stackoverflow.com/questions/1281140/run-process-with-realtime-output-in-php
   * Maybe ugly, but sometimes at 3AM it's only what is getting out of head ;-)
   */
  public function initScan() {
    echo '
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900" rel="stylesheet" />
  <link href="/resources/css/default.css" rel="stylesheet" type="text/css" media="all" />
  <link href="/resources/css/fonts.css" rel="stylesheet" type="text/css" media="all" />
<body style="background: #EDEDED">';
    set_time_limit(0);
    $handle = popen("host -a drukuj24.pl", "r");

    if (ob_get_level() == 0) {
      ob_start();
    }

    while (!feof($handle)) {

      $buffer = fgets($handle);
      $buffer = trim(htmlspecialchars($buffer));

      echo $buffer . "<br />";
      echo str_pad('', 4096);

      ob_flush();
      flush();
    }

    pclose($handle);
    ob_end_flush();
  echo'</body>';
  }


}