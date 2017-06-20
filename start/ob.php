<?php
/**
 * Created by PhpStorm.
 * User: nj
 * Date: 16/11/26
 * Time: 18:06
 * Desc: ob系列的函数,可能遇到的不多,我之前也没注意过,有用到的朋友可以看下
 */

### ob系列的函数使用场景
//ob的基本作用：
//  1)防止在浏览器有输出之后再使用setcookie()、header()或session_start()等发送头文件的函数造成的错误。其实这样的用法少用为好，养成良好的代码习惯。
//  2)捕捉对一些不可获取的函数的输出，比如phpinfo()会输出一大堆的html，但是我们无法用一个变量例如$info=phpinfo();来捕捉，这时候ob就管用了。
//  3)对输出的内容进行处理，例如进行gzip压缩，例如进行简繁转换，例如进行一些字符串替换。
//  4)生成静态文件，其实就是捕捉整页的输出，然后存成文件。经常在生成html，或者整页缓存中使用。
//对于刚才说的第三点中的gzip压缩，可能是很多人想用，却没有真用上的，其实稍稍修改下代码，就可以实现页面的gzip压缩。


//使用实例 ,借用知止的例子

ob_start(); //开启缓冲区,就是打开瓶子

echo 'once';

ob_flush(); //得到缓冲区的内容,并输出,就是把瓶子里的东西倒出来

echo 'twice';

$a = ob_get_contents(); //获取缓冲区的内容,需要显式输出,相当于拿到缓冲区的东西,但是不给,等你要的时候才给你
var_dump($a); //打印,相当于你问我要瓶子里的东西,我就给你了

echo 'third';
$len = ob_get_length();//获取缓冲区的数据长度,相当于数一下瓶子里还有几颗糖,哈哈

$status = ob_get_status();//得到输出缓冲区当前的状态,相当于看下瓶子有多大,已经使用了多少容量,里边有没有小瓶子
var_dump($status);

$level = ob_get_level(); //得到当前缓冲区的级别,好比是这是第几层瓶子
var_dump($level);

var_dump($len);
ob_end_flush(); //输出缓冲区的内容,并关闭缓冲区 相当于把瓶子里的东西都倒出来,并把瓶子扔掉

echo 123;

$error = ob_get_contents();
var_dump($error); //这里会报错,因为上边使用了ob_end_flush,瓶子已经扔掉了,再想要瓶子里的东西,不可能有了

ob_start();
echo 'test flush'; //不会被输出

ob_clean(); //清空缓冲区,相当于把瓶子里的东西扔掉,单瓶子仍然保留

echo 'test after clean';

$con = ob_get_contents();//把瓶子里的东西保存下来了

ob_end_clean(); //清空缓冲区,并关闭,相当于把瓶子里的东西连同瓶子一起扔掉

var_dump($con);//由于东西被保存,在此可以打印出来 相当于扔掉瓶子前,先把东西放到另一个地方了,还可以拿到

echo 'test after end_clean'; //不会被输出

ob_flush(); //会报错,因为上边已经使用了end关闭缓冲区,相当于瓶子被扔掉了,又想要里边的东西倒出来,已经晚了,哈哈~~~


## 完整的ob系列函数附在下边,需要的朋友可以看下

//flush(); //刷新输出缓冲
//
//ob_clean();  //清空刷出缓冲区
//
//ob_get_clean(); //清空并关闭缓冲区
//
//ob_end_flush(); //输出缓冲区内容并删除缓冲区
//
//ob_flush();  //输出缓冲区的内容
//
//ob_get_clean(); //得到当前缓冲区的内容并删除缓冲区
//
//ob_get_contents(); //得到缓冲区的内容
//
//ob_get_flush(); //输出缓冲区内容,并关闭缓冲区
//
//ob_get_length(); //返回缓冲区内容的长度
//
//ob_get_level(); //输出缓冲机制的嵌套级别
//
//ob_get_status(); //得到所有输出缓冲区的状态

//ob_gzhandler(); //在ob_start中用来压缩输出缓冲区中的内容时的回调函数
//
//ob_implicit_flush(); //打开/关闭绝对刷送
//
//ob_list_handlers(); //列出所有使用中的输出处理程序
//
//ob_start(); //打开输出缓冲控制
//
//output_add_rewrite_var(); //添加url重写器的值
//
//output_reset_rewrite_vars(); //重设url重写器的值


