<?php

define('DS', DIRECTORY_SEPARATOR);

class File
{
    private $dir = null;

    public function __construct($dir = ".\outputs")
    {
        $files = glob('*.zip');

        if (!isset($files[0]))
            die('<h2 style="text-align: center; margin-top: 100px; color: red;" dir="rtl">لطفا ابتدا فایل ارسال های نهایی ، دسته بندی شده بر اساس سوال را از وبسایت کویرا دانلود کرده و در دایرکتوری پروژه قرار بدهید :)</h2>');

        $this->dir = $dir;

        if (file_exists($dir))
            $this->delete_directory($dir);


        $zip = new ZipArchive;
        $res = $zip->open($files[0]);
        if ($res === TRUE) {
            $zip->extractTo($dir);
            $zip->close();
        }
    }

    public function delete_directory($dirname)
    {
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file))
                    unlink($dirname . "/" . $file);
                else
                    $this->delete_directory($dirname . '/' . $file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    public function get_exercises()
    {
        return array_slice(scandir($this->dir), 2);
    }

    public function get_homeworks($exercise)
    {
        return array_slice(scandir($this->dir . DS . $exercise), 2);
    }

    public function get_homework($exercise, $student_code)
    {
        $dir = $this->dir . DS . $exercise . DS . $student_code;

        $this->unzip($dir);

        $files = glob($dir . DS . 'project' . DS . '*.html');

        if (!isset($files[0]))
            $files = glob($dir . DS . 'project' . DS . '*' . DS . '*.html');

        return isset($files[0]) ? $files[0] : $dir . DS . 'project' . DS . 'index.html';
    }

    public function unzip($dir, $filename = 'project.zip')
    {
        $output = $dir . DS . 'project';


        $files = glob($dir . DS . '*.zip');

        if (file_exists($output))
            $this->delete_directory($output);

        $zip = new ZipArchive;
        $res = $zip->open(isset($files[0]) ? $files[0] : $dir . DS . $filename);
        if ($res === TRUE) {
            $zip->extractTo($output);
            $zip->close();
        }
    }
}
