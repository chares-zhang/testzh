<?php
/**
 * train
 *
 * @category   Page from ThinkPHP
 * @author     chareszhang<chareszhang@gmail.com>
 * @version    $Id: Page.php 2383 2011-09-16 07:54:44Z hsl $
 */

class Page{
    // 起始行数
    public $firstRow;
    // 列表每页显示行数
    public $listRows;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页栏每页显示的页数
    protected $rollPage   ;
	// 分页显示定制
    protected $config = array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %upPage% %first%  %prePage%  %linkPage%  %nextPage% %end% %downPage% <div id="total">%totalRow% %header% %nowPage%/%totalPage% 页</div>');

    /**
     +----------------------------------------------------------
     * 构造函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $rollPage  分页栏每页显示的页数
	 * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows,$rollPage=8,$parameter='') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->rollPage = $rollPage;
        $this->listRows = !empty($listRows)?$listRows:10;
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET['page'])?$_GET['page']:1;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p = 'page';
        $nowCoolPage = ceil($this->nowPage/$this->rollPage);
        $url = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url = $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="<li><a href='".$url."&amp;".$p."=$upRow' rel=prev>".$this->config['prev']."</a></li>";
        }else{
            $upPage="";
        }

        if ($downRow <= $this->totalPages){
            $downPage="<li><a href='".$url."&amp;".$p."=$downRow' rel=next>".$this->config['next']."</a></li>";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow = $this->nowPage-$this->rollPage;
            $prePage = "<li><a href='" . $url . "&amp;" . $p . "=$preRow'>上".$this->rollPage."页</a></li>";
            $theFirst = "<li><a href='".$url."&amp;".$p."=1' >".$this->config['first']."</a></li>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<li><a href='" . $url . "&amp;" . $p . "=$nextRow'>下".$this->rollPage."页</a></li>";
            $theEnd = "<li><a href='" . $url . "&amp;" . $p . "=$theEndRow' >" . $this->config['last'] . "</a></li>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= "<li><a href='".$url."&amp;".$p."=$page'>".$page."</a></li>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= '<li class="selected"><span class="current">' . $page ."</span></li>";
                }
            }
        }
        $pageStr = str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst, $prePage, $linkPage, $nextPage, $theEnd),$this->config['theme']);
        return $pageStr;
    }

}
?>
