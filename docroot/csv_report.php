<?php
/**
 * Created by PhpStorm.
 * User: Reese
 * Date: 9/29/18
 * Time: 12:51 PM
 */

class csv_report {

  private $header;

  private $output_header;

  protected $never_reported;

  protected $thirty_days_out;

  protected $full_report;

  protected $report;

  protected $empty_report;

  protected $cut_off_date;

  protected $days = 30;

  protected $dir = "../archive/";



  function __construct() {
    $fp = fopen($this->dir .'data.csv', 'r');
    $data = [];

    $this->header = fgetcsv($fp);
    // need 0,1,2,6
    // get data
    while (($input = fgetcsv($fp, 5000, ",")) !== FALSE) {
      $data[] = $input;
    }

    // Sort data
    // Only need rows where $data[6] < current date - 30 days
    $cut_off_date = strtotime("-{$this->days} days");

    foreach ($data as $row) {
      if (!empty($row[6])) {
        $date = str_getcsv($row[6], '/');
        $date[2] = substr($date[2], 0, 4);
        $last_reported_date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
        if ($last_reported_date < $cut_off_date) {
          $data = [
            $row[0],
            $row[1],
            $row[2],
            $row[6],
          ];
          $this->report[] = $data;
        }
      }
      else {
        $data = [
          $row[0],
          $row[1],
          $row[2],
          "Has never reported.",
        ];
        $this->empty_report[] = $data;
      }
    }
    //close
    fclose($fp);

    // Output a file!
    $this->output_header = [
      $this->header[0],
      $this->header[1],
      $this->header[2],
      $this->header[6],
    ];
  }

  public function create_reports() {
    $this->create_never_reported_csv();
    $this->create_thirty_days_out_csv();
    $this->create_full_csv();
  }

  protected function create_never_reported_csv() {
    $fp = fopen($this->dir .'never_reported_report.csv', 'w+');
    fputcsv($fp, $this->output_header);
    foreach ($this->empty_report as $item) {
      fputcsv($fp, $item);
    }
    fclose($fp);
  }

  protected function create_thirty_days_out_csv() {
    $fp = fopen($this->dir .'thirty_days_out_report.csv', 'w+');
    fputcsv($fp, $this->output_header);
    foreach ($this->report as $item) {
      fputcsv($fp, $item);
    }
    fclose($fp);
  }

  protected function create_full_csv() {
    $fp = fopen($this->dir .'full_report.csv', 'w+');
    fputcsv($fp, $this->output_header);
    foreach ($this->report as $item) {
      fputcsv($fp, $item);
    }
    foreach ($this->empty_report as $item) {
      fputcsv($fp, $item);
    }
    fclose($fp);
  }

}