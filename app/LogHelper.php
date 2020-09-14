<?php
namespace App;

use Illuminate\Support\Facades\Log;

class LogHelper
{
    /**
     * Log message of type info
     */
  const LOG_TYPE_INFO = 1;

    /**
     * Log message of type error
     */
  const LOG_TYPE_ERROR = 2;

  public static function output($message, $module = "", $logType = self::LOG_TYPE_INFO)
  {
    $moduleName = !empty($module) ? $module." | " : ""; 
    switch ($logType) {
      case self::LOG_TYPE_INFO:
        Log::info($moduleName.$message);
        break;
      case self::LOG_TYPE_ERROR:
        Log::error($moduleName.$message);
        break;
      default:
        Log::info($moduleName.$message);
        break;
    }
  }
}
