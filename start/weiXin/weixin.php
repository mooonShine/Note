<?php

/**
 *  微信网页授权单例
 *
 **/
class Com_WechatWebAuth
{
    // 微信app_id
    private $app_id = WECHAT_APP_ID;
    // 微信app_secret
    private $app_secret = WECHAT_APP_SECRET;
    // 对象保存地址
    private static $_instance;

    // 防止外部调用
    private function __construct()
    {

    }

    // 防止外部复制
    public function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

    // 创建单例对象
    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * 获取微信授权链接
     *
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     */
    public function get_authorize_url($redirect_uri = '', $scope = 'snsapi_base', $state = '')
    {
        $redirect_uri = urlencode($redirect_uri);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid 

={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
    }

    /**
     * 获取授权token
     *
     * @param string $code 通过get_authorize_url获取到的code
     */
    public function get_access_token($code = '')
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token? 
appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        $token_data = $this->http($token_url);
        if ($token_data[0] == 200) {
            return json_decode($token_data[1], TRUE);
        }

        return FALSE;
    }

    /**
     * 获取授权后的微信用户信息
     *
     * @param string $access_token
     * @param string $open_id
     */
    public function get_user_info($access_token = '', $open_id = '')
    {
        if ($access_token && $open_id) {
            $info_url = "https://api.weixin.qq.com/sns/userinfo?access_token 

={$access_token}&openid={$open_id}&lang=zh_CN";
            $info_data = $this->http($info_url);

            if ($info_data[0] == 200) {
                return json_decode($info_data[1], TRUE);
            }
        }

        return FALSE;
    }

    /**
     * 刷新access_token
     * @param string $refresh_token
     */
    public function refresh_token( $refresh_token = '' ) {
        if ( $refresh_token ) {
            $info_url  = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid 

={$this->app_id}&grant_type=refresh_token&refresh_token={$refresh_token}";
            $info_data = $this -> http( $info_url );

            if ( $info_data['access_token'] ) return TRUE;
        }

        return FALSE;
    }

    /**
     * 获取access_token( 文件缓存 )
     */
    public function get_normal_access_token()
    {
        // 获取上次缓存的access_token
        $normal_access_token = file_get_contents('wechatsession/ticket_access_token.txt');
        $normal_access_token = json_decode($normal_access_token, TRUE);
        $time = time();
        // 判断缓存是否超时
        if (is_null($normal_access_token) || $time > $normal_access_token['expires_in']) {
            $token_data = curl_https_file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid 

=' . $this -> app_id . '&secret=' . $this -> app_secret); // 获取access_token
            $token_data = json_decode($token_data, TURE);
            $access_token = $token_data['access_token']; // 输出access_token
            $expires_in = $time + 7000;
            file_put_contents('wechatsession/ticket_access_token.txt', json_encode(array('access_token' => $access_token, 'expires_in' => $expires_in)));
            return $access_token;
        } else {
            return $normal_access_token['access_token'];
        }
    }

    /**
     * 获取ticket( 文件缓存 )
     * @param $access_token
     */
    public function get_ticket($access_token)
    {
        if ($access_token) {
            // 获取上次缓存的ticket
            $ticket = file_get_contents('wechatsession/ticket.txt');
            $ticket = json_decode($ticket, TRUE);
            $time = time();
            // 判断缓存是否超时
            if (is_null($ticket) || $time > $ticket['expires_in']) {
                $new_ticket_result = curl_https_file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token 

=' . $access_token . '&type=jsapi'); // 获取access_token
                $new_ticket_result = json_decode($new_ticket_result, TRUE);
                $new_ticket = $new_ticket_result['ticket']; // 输出access_token
                $expires_in = $time + 7000;
                file_put_contents('wechatsession/ticket.txt', json_encode(array('ticket' => $new_ticket, 'expires_in' => $expires_in)));
                return $new_ticket;
            } else {
                return $ticket['ticket'];
            }
        } else {
            return FALSE;
        }
    }

    public function http( $url, $method = 'get', $postfields = null, $headers = array(), $debug = false ) {
        $ci = curl_init();
        /* Curl settings */
        curl_setopt( $ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ci, CURLOPT_CONNECTTIMEOUT, 30 );
        curl_setopt( $ci, CURLOPT_TIMEOUT, 30 );
        curl_setopt( $ci, CURLOPT_RETURNTRANSFER, true );

        switch ( $method ) {
            case 'POST':
                curl_setopt( $ci, CURLOPT_POST, true );
                if ( !empty( $postfields ) ) {
                    curl_setopt( $ci, CURLOPT_POSTFIELDS, $postfields );
                    $this -> postdata = $postfields;
                }
                break;
        }
        curl_setopt( $ci, CURLOPT_URL, $url );
        curl_setopt( $ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ci, CURLINFO_HEADER_OUT, true );

        $response = curl_exec( $ci );
        $http_code = curl_getinfo( $ci, CURLINFO_HTTP_CODE );

        if ( $debug ) {
            echo "=====post data======\r\n";
            var_dump( $postfields );

            echo '=====info=====' . "\r\n";
            print_r( curl_getinfo( $ci ) );

            echo '=====$response=====' . "\r\n";
            print_r( $response );
        }
        curl_close( $ci );
        return array( $http_code, $response );
    }

    // 检查是否已获取微信openid
    public function chaeck_openid() {
        if ( $this -> __authuser['openid'] ) {
            return true;
        } else {
            return false;
        }
    }

}

