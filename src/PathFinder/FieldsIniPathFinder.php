<?php
namespace Qobo\Utils\PathFinder;

use Cake\Core\Configure;

/**
 * FieldsIniPathFinder Class
 *
 * This path finder is here to assist with finding
 * the paths to the module fields configuration files.
 * If no $path is specified, then the path to the
 * default fields configuration file (fields.ini) is
 * returned.
 */
class FieldsIniPathFinder extends ConfigPathFinder
{
    protected $fileName = 'fields.ini';
}
