<?php namespace EscapeWork\Assets;

use File;

class SymLinker
{

    public function link($target, $link)
    {
        $base_dir = explode('/', $link);
        array_pop($base_dir);
        File::makeDirectory(implode('/', $base_dir), 0755, true);

        if (is_dir($target)) {
            symlink($target, $link);
        }
    }

    public function unlink($link)
    {
        if (is_dir($link)) {
            unlink($link);
        }
    }
}
