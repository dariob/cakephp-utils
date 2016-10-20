<?php
namespace CsvMigrations\PathFinder;

/**
 * PathFinderInterface Interface
 *
 * This interface defines the standard approach for
 * finding paths (files and directories) in standard
 * places, with a bit of flexibility for custom
 * situations.
 *
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
interface PathFinderInterface
{
    /**
     * Find path
     *
     * Most files will require the $module parameter to
     * make search more specific.  The only files that
     * are currently module-independent are list CSVs.
     *
     * @param string $module   Module to look for files in
     * @param string $path     Path to look for
     * @return null|string|array Null for not found, string for single path, array for multiple paths
     */
    public function find($module = null, $path = null);
}
