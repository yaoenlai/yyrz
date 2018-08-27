<?php
namespace app\exception;

use think\exception\Handle;
class BaseHandle extends Handle
{
    
    public function render(\Exception $e)
    {
        die(json_encode(array(
            'code'      =>  500,                     //$e->getCode(),
            'message'   =>  $e->getMessage(),
            'data'      =>  array(
                'file'=>$e->getFile(),
                'line'=>$e->getLine(),
                'previous'=>$e->getPrevious(),
            )
        )));
    }
}