<?php
/**
 * Created by PhpStorm.
 * User: baocaizai
 * Date: 16/7/20
 * Time: 上午9:16
 */

namespace Org\Util;


class Word
{
    function start(){
        ob_start(); //打开输出控制缓冲
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"';
        echo 'xmlns:w="urn:schemas-microsoft-com:office:word"';
        echo 'xmlns="http://www.w3.org/TR/REC-html40">';
    }
    function save($path){
        echo "</html>";
        $data=ob_get_contents();    //返回输出缓冲区的内容
        ob_end_clean();             //清空缓冲区并关闭输出缓冲
        $this->writeFile($path,$data);   //将缓冲区内容写入word
    }
    function writeFile($fn,$data){
        $fp=fopen($fn,"wb+");
        fwrite($fp,$data);
        fclose($fp);
    }
}