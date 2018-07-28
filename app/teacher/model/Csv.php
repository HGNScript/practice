<?php
/**
 * Created by PhpStorm.
 * User: HGN
 * Date: 2018/7/28
 * Time: 14:09
 */

namespace app\teacher\model;


class Csv
{
    //导出csv文件
    public function put_csv($list,$title)
    {
        $file_name = "exam".time().".csv";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$file_name );
        header('Cache-Control: max-age=0');
        $file = fopen('php://output',"a");
        $limit = 1000;
        $calc = 0;
        foreach ($title as $v){
            $tit[] = iconv('UTF-8', 'GB2312//IGNORE',$v);
        }
        fputcsv($file,$tit);

        $tarr = [];
        foreach ($list as $v){
            $calc++;
            if($limit == $calc){
                ob_flush();
                flush();
                $calc = 0;
            }
            foreach($v as $t){
                $tarr[] = iconv('UTF-8', 'GB2312//IGNORE',$t. "\t");
            }


            fputcsv($file,$tarr);
            unset($tarr);
        }
        unset($list);
        fclose($file);
        exit();
    }

}