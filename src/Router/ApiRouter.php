<?php
namespace Nest\Router;

class ApiRouter {
    private $filePath;
    public function __construct($pathDir, $pathFile)
    {
        $this->filePath = DEF_DOC_ROOT.'/api/'.$pathDir.'/'.$pathFile.'.php';
    }

    public function doRequestProcess()
    {
        if(file_exists($this->filePath))
        {
            return $this->filePath;
        }
        return '';
    }
}