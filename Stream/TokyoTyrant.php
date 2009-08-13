<?php
//require_once "Net/TokyoTyrant.php";

class Stream_TokyoTyrant
{
    const protocolname = 'tokyotyrant';
    
    private
        $tt,
        $port,
        $server,
        $positon,
        $data,
        $key;

    public function  __construct()
    {

    }

    public static function register()
    {
        //FOOOO!!!!!
        stream_wrapper_register(self::protocolname, __CLASS__);
    }

    
    protected function getTokyoTyrant($port, $server, $options = array())
    {   
        static $instance = array();
        $name = sprintf('%s,%s', $server, $port);
        if (isset($instance[$name]) === false) {
            $instance[$name] = new Net_TokyoTyrant();
            $instance[$name]->connect($server, $port);
        }
        return $instance[$name];
    }
    
    protected function nomalizePath($path)
    {
        return ltrim($path, '/');
    }

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $port = $url['port'];
        $server = $url['host'];
        try {
            $this->tt = $this->getTokyoTyrant($port, $server);
        } catch (Exception $e){
            return false;
        }
        $this->key = $this->nomalizePath($url['path']);
        $this->data = $this->tt->get($this->key);
        $this->position = 0;
        return true;
    }
 
    function stream_read($count)
    {
        $ret = substr($this->data, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    function stream_write($data)
    {
        $left = substr($this->data, 0, $this->position);
        $right = substr($this_data, $this->position + strlen($data));
        $this->data = $left . $data . $right;
        $this->position += strlen($data);
        $this->tt->put($this->key, $this->data);
        return strlen($data);
    }

    function stream_tell()
    {
        return $this->position;
    }

    function stream_eof()
    {
        return $this->position >= strlen($this->data);
    }

    function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($this->data) && $offset >= 0) {
                     $this->position = $offset;
                     return true;
                } else {
                     return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                     $this->position += $offset;
                     return true;
                } else {
                     return false;
                }
                break;

            case SEEK_END:
                if (strlen($this->data) + $offset >= 0) {
                     $this->position = strlen($this->data) + $offset;
                     return true;
                } else {
                     return false;
                }
                break;

            default:
                return false;
        }
    }

 }
