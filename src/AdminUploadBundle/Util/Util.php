<?php

namespace AdminUploadBundle\Util;

use Cocur\Slugify\Slugify;

/**
 * Class Util
 */
class Util
{
    /**
     * @param string $fileName
     * @return string
     */
    public static function getFileName(string $fileName)
    {
        return (new Slugify())->slugify($fileName, ['regexp' => '/([^A-Za-z0-9\.]|-)+/']);
    }

    /**
     * @param string $fileName
     * @param string $alias
     * @return string
     */
    public static function getFilePath(string $fileName, string $alias)
    {
        return '/' . trim($alias, '/') . '/' . $fileName;
    }
}
