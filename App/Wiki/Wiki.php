<?php
namespace App\Wiki;
use App\Core\BaseController;
use App\Conf\Conf;
use App\Sec\Sec;
error_reporting(0);

class Wiki extends BaseController
{
    private $Sec;

    /**
     * wiki constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->Sec = new Sec();
    }

    /**
     * User: youranreus
     * Date: 2021/3/16 14:52
     * @param $page
     */
    public function getWikiList($page){
        $page = ($page-1) * Conf::$WikiPageLimit;
        if(isset($_GET['cate'])){
            $Wikis = $this->database->select("wiki",["id","title","cate","date"], [
                "cate" => $_GET["cate"],
                "LIMIT" => [$page , Conf::$WikiPageLimit]
            ]);
            exit(json_encode($Wikis));
        }
        $Wikis = $this->database->select("wiki", "*",["LIMIT" => [$page , Conf::$WikiPageLimit]]);
        exit(json_encode($Wikis));
    }

    /**
     * @param $id
     * User: youranreus
     * Date: 2020/12/22 18:55
     */
    public function getWikiDetail($id)
    {
        if(!isset($id)){
            exit(json_encode(array("msg"=>Conf::$WikiIDMissing)));
        }
        $Wiki = $this->database->select("wiki","*", [
            "id" => $id
        ]);
        exit(json_encode($Wiki));
    }

    /**
     * @return array
     * User: youranreus
     * Date: 2020/12/22 18:55
     */
    public function postWiki(): array
    {

        $this->Sec->accessCheck("post");

        if(!isset($_POST["author"]) or !isset($_POST["title"]) or !isset($_POST["cate"]) or !isset($_POST["likes"]) or !isset($_POST["contents"])){
            exit(json_encode(array("msg"=>Conf::$msgOnParamMissing)));
        }

        $this->database->insert("wiki", [
            "title" => $_POST["title"],
            "date" => date("yy-m-d"),
            "cate" => $_POST["cate"],
            "author"=>$_POST["author"],
            "likes"=>$_POST["likes"],
            "contents"=>$_POST["contents"],
        ]);

        exit(json_encode(array("msg"=>"ok","id"=>$this->database->id())));
    }

    /**
     * User: youranreus
     * Date: 2021/3/16 14:49
     * @param $id
     */
    public function deleteWiki($id){

        $this->Sec->accessCheck("get");

        if(!isset($id)){
            exit(json_encode(array("msg"=>Conf::$msgOnParamMissing)));
        }

        $action=$this->database->delete("wiki", [
            "id" => $id
        ]);

        exit(json_encode(array("msg"=>"ok","rows"=>$action->rowCount())));
    }

    /**
     * @param $keyword
     * User: youranreus
     * Date: 2021/3/19 12:14
     */
    public function search($keyword)
    {
        $keywords = explode(" ",urldecode($keyword));
        exit(json_encode($this->database->select(
            "wiki",
            "*",
            ["title[~]" => $keywords]
        )));
    }

}