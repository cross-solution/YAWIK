<?php

namespace Pdf\extern;

//use \vendor\mpdf\mpdf\mpdf as mPDF;

class mPDFderive extends \mPDF
{
    function Error($msg)
    {
    //Fatal error
        throw new \InvalidArgumentException($msg);
    }
}
