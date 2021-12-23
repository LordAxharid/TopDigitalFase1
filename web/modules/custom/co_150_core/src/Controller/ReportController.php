<?php

/**
 * @file
 * @author David Martinez
 * Contains \Drupal\co_150_core\Controller\GameController.
 * Please place this file under your example(module_root_folder)/src/Controller/
 * Use https://www.drupal.org/docs/8/api/database-api/dynamic-queries/introduction-to-dynamic-queries  para consultes y actualización de data
 */

namespace Drupal\co_150_core\Controller;

use Drupal\co_150_core\Core\XLSXWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportController
{

  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
  }

  /**
   * excel
   *
   * @return void
   */
  public function export($table)
  {
    $this->table = $table;
    list($header, $rows) = $this->getData(false);
    $filename = 'export_' . $this->table . '_' . date('Y_m_d_H_i_s') . '.csv';
    // print(json_encode($rows));die;
    $temp_file = $this->_write_and_download_xlsx($header, $rows);
    $headers = [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'Content-Disposition' => 'attachment; filename=' . XLSXWriter::sanitize_filename($filename),
    ];
    // var_dump($headers);die;
    return new BinaryFileResponse($temp_file, 200, $headers, FALSE);
  }

  /**
   * _write_and_download_xlsx
   *
   * @param  mixed $header
   * @param  mixed $rows
   * @return void
   */
  private function _write_and_download_xlsx($header, $rows)
  {
    $header_types = [];
    foreach ($header as $key => $header_item)  $header_types[$header_item] = 'string';
    $writer = new XLSXWriter();
    $writer->setAuthor('150Porciento');
    $writer->writeSheet($rows, 'Hoja 1', $header_types);
    // Create a temp file and write
    $temp_dir = sys_get_temp_dir();
    $temp_file = tempnam($temp_dir, "150porciento_xlsx_writer_");
    $writer->writeToFile($temp_file);
    return $temp_file;
  }


  /**
   * getData
   *Get data From DB
   * @param  mixed $excel
   * @return void
   */
  private function getData($excel = false)
  {
    $con = \Drupal\Core\Database\Database::getConnection();
    $fieldsDB = $con->query("DESCRIBE `{$this->table}`")->fetchAll();
    $fields = [];
    foreach ($fieldsDB as $key => $value) {
      $fields[] = $value->Field ;
    }
    $fields[] = 'options';

    $idPrincipal = array_search('id', $fields) === false ? $fields[0] : 'id';

    $query = \Drupal::database()->select($this->table, 'r');
    $result = $query->fields('r')->orderBy($idPrincipal, 'DESC')->execute();
    $rows = $header = [];
    foreach ($result as $item) {
      if (!$header)   $header = array_keys((array) $item);
      $row = (array) $item;
      if (isset($row['created_at'])) {
        if(is_numeric($row['created_at'])) {
          $row['created_at'] = date('r', $row['created_at']);
        }
      }
      $rows[] = $row;
    }
    return [$header, $rows];
  }
}
