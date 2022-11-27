<?php
namespace AiPictures;

/**
 * Class Creator
 * @package AiPictures
 */
class Creator
{
    protected $config;

    /**
     * Creator constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 获取AccessToken
     * @return false|mixed|string
     */
    public function getAccessToken()
    {
        $body = $this->get('https://aip.baidubce.com/oauth/2.0/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
        ]);
        $result = json_decode($body, true);
        return $result ? : $body;
    }

    /**
     * 文生图
     * @param $text
     * @param $resolution
     * @param $style
     * @param $accessToken
     * @return false|mixed|string
     */
    public function create($text, $resolution, $style, $accessToken)
    {
        $body = $this->post(
            'https://aip.baidubce.com/rpc/2.0/ernievilg/v1/txt2img?' . $accessToken,
            [
                'text' => $text,
                'resolution' => $resolution,
                'style' => $style
            ]
        );
        $result = json_decode($body, true);
        return $result ? : $body;
    }

    /**
     * 获取文生图结果
     * @param $taskId
     * @param $accessToken
     * @return false|mixed|string
     */
    public function result($taskId, $accessToken)
    {
        $body = $this->post(
            'https://aip.baidubce.com/rpc/2.0/ernievilg/v1/getImg?' . $accessToken,
            [
                'taskId' => $taskId,
            ]
        );
        $result = json_decode($body, true);
        return $result ? : $body;
    }

    /**
     * @param $url
     * @param array $param
     * @return false|string
     */
    private function get($url, array $param = [])
    {
        if($param){
            $url .= '?' . http_build_query($param);
        }
        return file_get_contents($url);
    }

    /**
     * @param $url
     * @param array $param
     * @return false|string
     */
    private function post($url, array $param = [])
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'content-type:application/json',
                'content' => json_encode($param, JSON_UNESCAPED_UNICODE),
                'timeout' => 5
            ]
        ]);
        return file_get_contents($url, false, $context);
    }
}