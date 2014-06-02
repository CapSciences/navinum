<?php

class mpExportRealityTypes
{
  const EXPORT_TYPE_XLS = 'xls';
  const EXPORT_TYPE_CSV = 'csv';
  const EXPORT_TYPE_XML = 'xml';
  const EXPORT_TYPE_HTML = 'html';
  const EXPORT_TYPE_PDF = 'pdf';

  static protected
  $types = array(
      self::EXPORT_TYPE_XLS   => 'Excel',
      self::EXPORT_TYPE_CSV   => 'CSV',
      self::EXPORT_TYPE_XML   => 'XML',
      self::EXPORT_TYPE_HTML  => 'HTML',
      self::EXPORT_TYPE_PDF   => 'PDF',
  );

  static public function getTypes()
  {
    return array_keys(self::$types);
  }

}
