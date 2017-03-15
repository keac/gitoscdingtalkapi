<?php
/**
 * Created by PhpStorm.
 * User: keac
 * Date: 2017/3/14 0014
 * Time: 14:09
 */

$git_Data=json_decode($GLOBALS['HTTP_RAW_POST_DATA']);

if($git_Data->password=="Northme"){ //Set password
$hook_name=$git_Data->hook_name;

/*Push*/
function fnPush($data){
$commit_arr=$data -> commits;
$commit_messages="";
foreach ($commit_arr as $commit)
{
    $commit_messages.="
commits id：".$commit -> id ."\n
commits message：".$commit -> message ."\n
commits 时间：".$commit ->timestamp ."\n
";
}
$messages=
 "Git@OSC ".$data -> repository -> name."Push 提示\n
分支：".$data -> ref ."\n
提交者：".$data -> user_name ."\n
提交时间：".$data -> user -> time ."\n
Commits个数：".$data -> total_commits_count ."\n
---详情---
$commit_messages
---仓库信息--
仓库名称：".$data -> repository -> name ."\n
仓库地址：".$data -> repository -> url ." \n
仓库描述：".$data -> repository -> description." \n
仓库主页：".$data -> repository -> homepage ."\n
";
return $messages;
}
/* Note */
function fn_Note($data){

    $messages="
Git@osc ".$data -> noteable_type ." 提示\n
内容：".$data -> note ."\n
Id：".$data -> noteable_id ."\n
作者：".$data -> author -> user_name."\n
地址：".$data -> url."\n
---项目--
项目名称：".$data -> project ->name ."\n
项目路径：".$data -> project ->path ."\n
项目描述：".$data -> project ->description ."\n
项目地址：".$data -> project ->url ."\n
项目Git：".$data -> project ->git_http_url ."\n
---仓库--
仓库名称：".$data -> repository ->name ."\n
仓库链接：".$data -> repository ->url ."\n
仓库描述：".$data -> repository ->description ."\n
";
    return $messages;
}

/* PullRequest */
function fnMerge_request($data){
    $messages="
Git@osc PullRequest 提示\n
iid：".$data -> iid ."\n
title：".$data -> title ."\n
body：".$data -> body ."\n
state：".$data -> state ."\n
merge_status：".$data -> merge_status ."\n
Note作者：".$data -> author -> user_name."\n
Note地址：".$data -> url."\n
key1：".$data -> key1."\n
key2：".$data -> key2."\n

源分支：".$data -> source_branch ."\n
---源项目--
源项目名称：".$data ->source_repo -> project ->name ."\n
源项目路径：".$data ->source_repo -> project ->path ."\n
源项目描述：".$data ->source_repo -> project ->description ."\n
源项目地址：".$data ->source_repo -> project ->url ."\n
源项目Git：".$data ->source_repo -> project ->git_http_url ."\n
源项目命名空间：".$data ->source_repo -> project ->namespace ."\n
源项目有路径的空间：".$data ->source_repo -> project ->path_with_namespace ."\n
源项目分支：".$data ->source_repo -> project ->default_branch ."\n
---源仓库--
源仓库名称：".$data ->source_repo ->  repository ->name ."\n
源仓库链接：".$data ->source_repo ->  repository ->url ."\n
源仓库描述：".$data ->source_repo ->  repository ->description ."\n

---目标仓库--
目标分支：".$data -> target_branch ."\n
---目标项目--
目标项目名称：".$data ->target_repo -> project ->name ."\n
目标项目路径：".$data ->target_repo -> project ->path ."\n
目标项目描述：".$data ->target_repo -> project ->description ."\n
目标项目地址：".$data ->target_repo -> project ->url ."\n
目标项目Git：".$data ->target_repo -> project ->git_http_url ."\n
目标项目命名空间：".$data ->target_repo -> project ->namespace ."\n
目标项目有路径的空间：".$data ->target_repo -> project ->path_with_namespace ."\n
目标项目分支：".$data ->target_repo -> project ->default_branch ."\n
---目标仓库--
目标仓库名称：".$data -> target_repo-> repository ->name ."\n
目标仓库链接：".$data -> target_repo-> repository ->url ."\n
目标仓库描述：".$data -> target_repo-> repository ->description ."\n

";
    return $messages;
}

/* Issue */
function fn_Issue($data){

 $messages="
Git@osc Issue 提示\n
iid：".$data -> iid ."\n
名称：".$data -> title ."\n
描述：".$data -> description ."\n
状态：".$data -> state ."\n
assignee：".$data -> assignee."\n
milestone：".$data -> milestone."\n
提起者：".$data -> user -> username ."\n

---项目--
项目名称：".$data -> project ->name ."\n
项目路径：".$data -> project ->path ."\n
项目描述：".$data -> project ->description ."\n
项目地址：".$data -> project ->url ."\n
项目Git：".$data -> project ->git_http_url ."\n
项目分支：".$data -> project ->default_branch ."\n

---仓库--
仓库名称：".$data -> repository ->name ."\n
仓库链接：".$data -> repository ->url ."\n
仓库描述：".$data -> repository ->description ."\n
";
        return $messages;
    }
    /*Tag*/
function fn_Tag($data){

 $messages="
Git@osc 新增 Tag  提示\n
ref：".$data -> ref ."\n
before：".$data -> before ."\n
after：".$data -> after ."\n
---项目--
项目名称：".$data -> project ->name ."\n
项目路径：".$data -> project ->path ."\n
项目描述：".$data -> project ->description ."\n
项目地址：".$data -> project ->url ."\n
项目Git：".$data -> project ->git_http_url ."\n
项目分支：".$data -> project ->default_branch ."\n
---仓库--
仓库名称：".$data -> repository ->name ."\n
仓库链接：".$data -> repository ->url ."\n
仓库描述：".$data -> repository ->description ."\n
";
 return $messages;
    }
switch ($hook_name){
    case "push_hooks":
        $messages=fnPush($git_Data);
        break;
    case "note_hooks":
        $messages=fn_Note($git_Data);
        break;
    case "merge_request_hooks":
        $messages=fnMerge_request($git_Data);
        break;
    case "issue_hooks":
        $messages=fn_Issue($git_Data);
        break;
    case "tag_push_hooks":
        $messages=fn_Tag($git_Data);
        break;

    default:
    $messages="Error ! The ip is ".$_SERVER["REMOTE_ADDR"];
    break;
}


/*************
 *
 *  To DingTalk
 *
 ************/
$access_token="";
$isAtAll=false;
$data = array ('msgtype' => 'text','text' => array ('content' => $messages,),'at' => array ('atMobiles' => array (),'isAtAll' => $isAtAll,),);
$data_string = json_encode($data);
$urls="https://oapi.dingtalk.com/robot/send?access_token=".$access_token;
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json; charset=utf-8'));
curl_setopt($ch, CURLOPT_URL, "$urls");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
$retBase = curl_exec($ch);
curl_close($ch);
}
