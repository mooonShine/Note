/**
 * 下载图片
 */
public function downLoadImgAction()
{
    if ($this->_request->isPost()) {
        $img_name = $this->_request->getPost('name');
        $img_name = 'http://'.$_SERVER['SERVER_NAME'].$img_name;
        if (! $img_name ) fn_ajax_return(0,'参数错误','');
        $file_name = date('YmdHis',time()).mt_rand(10000,100000).'.'.fn_get_fileExt($img_name);
        header('content-type: application/octet-stream');
        header('Content-Disposition: attachment;filename='.$file_name);
        $fr = file_get_contents( $img_name );
        echo $fr;
    }
}