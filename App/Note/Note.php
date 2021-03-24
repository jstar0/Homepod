<?php


namespace App\Note;
use App\Conf\Conf;
use Medoo\Medoo;


class Note
{
    private $database;

    /**
     * Note constructor.
     */
    public function __construct()
    {
        $this->database = new medoo([
            'database_type' => 'mysql',
            'database_name' => Conf::$dbname,
            'server' => Conf::$servername,
            'username' => Conf::$username,
            'password' => Conf::$password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ]);
    }

    /**
     * @param $sid
     * User: youranreus
     * Date: 2021/3/23 16:29
     */
    public function getNote($sid)
    {

        if($this->database->has("note", ["sid"=>$sid]))
        {
            exit(json_encode($this->database->select("note", ["content"], [
                "sid" => $sid
            ])));
        }
        else
        {
            exit(json_encode($this->createNote($sid)));
        }

    }

    /**
     * @param $sid
     * @return array
     * User: youranreus
     * Date: 2021/3/23 16:42
     */
    public function createNote($sid): array
    {
        $key = $this->createKey();
        $this->database->insert("note",[
            "sid"=>$sid,
            "content"=>"Begin your story.",
            "key"=> ""
        ]);

        return ["content"=>"Begin your story.","key"=>""];
    }

    /**
     * @param int $length
     * @return false|string
     * User: youranreus
     * Date: 2021/3/23 16:40
     */
    private function createKey($length=5)
    {
        return substr(md5(time()), 0, $length);
    }

    /**
     * @param $sid
     * User: youranreus
     * Date: 2021/3/23 16:47
     */
    public function deleteNote($sid)
    {
        $this->checkKey($sid);

        $data = $this->database->delete("note", [
            "sid"=>$sid
        ]);

        exit(json_encode($data->rowCount()));
    }

    /**
     * @param $sid
     * User: youranreus
     * Date: 2021/3/23 17:40
     */
    public function modifyNote($sid)
    {

        $haveKey = $this->haveKey($sid);
        if($haveKey != false)
        {
            $this->checkKey($haveKey);
        }
        else
        {
            $this->database->update("note",["key"=>$_GET["key"]],["sid"=>$sid]);
        }


        if(!isset($_POST["content"]))
        {
            exit(json_encode("参数缺失"));
        }

        $result = $this->database->update("note",["content"=>$_POST["content"]],["sid"=>$sid]);

        exit(json_encode($result->rowCount()));
    }

    /**
     * User: youranreus
     * Date: 2021/3/23 17:00
     * @param $key
     */
    private function checkKey($key)
    {
        if(!isset($_GET["key"]))
        {
            exit(json_encode("密钥缺失"));
        }
        if ($key != $_GET["key"])
        {
            exit(json_encode("密钥错误或对应笔记不存在"));
        }
    }

    /**
     * @param $sid
     * @return bool
     * User: youranreus
     * Date: 2021/3/24 19:22
     */
    private function haveKey($sid): bool
    {
        $key = $this->database->select("note","key",['sid'=>$sid]);
        if($key[0] != "")
        {
            return $key[0];
        }
        return false;
    }


}