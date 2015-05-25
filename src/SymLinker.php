<?php namespace EscapeWork\Assets;

class SymLinker
{

    public function link($target, $link)
    {
        symlink($origin_dir, $dist_dir);
    }

    public function unlink($link)
    {
        if (is_dir($link)) {
            unlink($link);
        }
    }
}
