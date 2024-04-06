<?php
namespace Jiny\WireTable\Http\Trait;

use Illuminate\Support\Facades\DB;

trait Tabbar
{
    /** ----- ----- ----- ----- -----
     *  Tab Title Setting popup
     */
    public $popupTabbar = false;
    public $popupTabbarMessage;
    public $popupTabbarConfirm = false;
    public $tabname;
    public $tabid;
    public function popupTabbarClose()
    {
        $this->popupTabbar = false;
        $this->tabid = null;
        $this->tabname = null;
        $this->popupTabbarConfirm = false;
        $this->popupTabbarMessage = null;
    }
    public function popupNewTab()
    {
        $this->tabid = null;
        $this->tabname = null;

        // 팝업창 열기
        $this->popupTabbar = true;
    }

    public function popupTabEdit($id=null)
    {
        if($id) {
            $this->tabid = $id;
            $tab = DB::table('form_tabs')->where('id',$id)->first();
            $this->tabname = $tab->name;
        }

        $this->popupTabbar = true;
    }

    public function popupTabbarSave()
    {
        if($this->tabid) {
            DB::table('form_tabs')->where('id',$this->tabid)->update(['name'=>$this->tabname]);

        } else {
            $uri = "/".$this->actions['route']['uri'];
            $pos = DB::table('form_tabs')->where('uri',$uri)->max('pos'); //최대값 pos

            DB::table('form_tabs')->insert([
                'uri'=> $uri,
                'name'=> $this->tabname,
                'pos'=> $pos+1
            ]);
        }

        $this->popupTabbarClose();
    }

    public function popupTabbarDelete()
    {
        if($this->tabid) {
            $this->popupTabbarMessage = "정말 삭제하시겠습니까?";
            if($this->popupTabbarConfirm) {

                $rows = DB::table('table_forms')->where('tab', $this->tabid)->get();
                if(count($rows)>0) {
                    $this->popupTabbarMessage = "텝에 소속된 항목이 있어 삭제할 수 없습니다.";
                    //dd($rows);

                } else {
                    DB::table('form_tabs')
                    ->where('id', $this->tabid)
                    ->delete();

                    $this->popupTabbarClose();
                }


            } else {
                $this->popupTabbarConfirm = true; //confirm
            }
        } else {
            $this->popupTabbarMessage = "삭제할 텝이 선택되지 않았습니다.";
        }
    }
}
