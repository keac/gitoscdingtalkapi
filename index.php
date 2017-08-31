<?php
/**************************************
 *
 * Created by PhpStorm.
 * User: keac
 * Date: 2017/3/14 0014
 * Time: 14:09
 * Email:admin@northme.com
 * Hangzhou Northme Technology Co. Ltd
 *
 *  @@@@@@@@@@@@~~:-:~:::::~~:;;;@@@@@@@@@@@@@@@@,@@@,@@@@@@@@@@,@@,,,,,,,,@@@ ,,,,,,@-,@@@,@@@@@@,@@@@@@,,@@@@@@@@@@@@@@@@@
 *  @@@@@@@@@@@~:~~~~::;:::::~:;;;@@@@@@@@@@@@@@@,@@@,@@@@@@@@@,,@@@@@@@@,-@@@-,,,,@@@@,,@@,@@@@@@,@@@@@@,,@@@@@@@@@@@@@@@@@
 *  @@@@@@@@@@:::;::~~:~::::;~:;;;!@@@@@@@@@@@@@@,@@@,@@@@@@@@,,@@@@@@@@,,@@@@@@@,,@@@@@,,@,@@@@@@,@@,,,,,,,,,,@@@@@@@@@@@@@
 *  @@@@@@@@@::::::::;:;:;;;;;!;;!;;@@@@@@@@@@@@@,@@@,@@@@,@@,,@@@@@@@@,,@@@@@@@@,,@@@@@@,@,@@@@,,,,,@@@@,,@@@@@@@@@@@@@@@@@
 *  @@@@@@@@::@@@@@@:::::;;;;;!:;;;;@@@@@@@@,,,,,,@@@,@@,,,@@,@@@,@@@@,,,,@@@@,,,,,,,@,@@@@,@@@@@@,@@@@@@,,@@@@@@@@@@@@@@@@@
 *  @@@@@@@@:~;:;:@@@;;:;:;!;;;;;!;;*@@@@@@@@@@@@,@@@,,,,@@@@@@@,,@@@,,@@,,@@@@@@,,@@@,,@@@,@@@@@@,@@@@@@,,@@@@@@@@@@@@@@@@@
 *  @@@@@@@~::~:;:@@@@;:;;;;;!;;;!::;@@@@@@@@@@@@,@@@,@@@@@@@@@,,@-,,,@@@@,,,@@@@,,~@@@,,~@,@@@@@@,@,@,,,,,,,,@@@@@@@@@@@@@@
 *  @@@@@@@;::::@@@@@@@:;!:;:;;!;;;:;@@@@@@@@@@@@,@@@,@@@@@@@@,,,@,-@@@@@@@@@@@@,,,,-@@@,,@,@@@@@@,,,@@,@@@@@,@@@@@@@@@@@@@@
 *  @@@@@@@;;:@@@@@@@@@@;;;:;!!!;;!!;*@@@@@@@@@@@,@@@,@@@@@@@,,,,@@,,,,,,,,,@@@,,,,@,,@@@@@,@@@-,,,@@@@,-@@@,,@@@@@@@@@@@@@@
 *  @@@@@@;;!@@@@@@@@@@@@!;;!;!!!;!!!;@@@@@@@@@@@,@@@,@@@@@@@-@,,@@----,----@@-,@,,@@@@-,,,,,,@,@@,@@@@@,@@-,@@@@@@@@@@@@@@@
 *  @@@@@@;;@@@@@@@@@@@@@@*;;!;;!!;!*!@@@@@@@@@@@,@@@,@@@@,@@@@,,@@@@@@,@@@@@@,@@,,@@,,,,,@,@@@@@@,@@@@@,,@, @@@@@@@@@@@@@@@
 *  @@@@@@;~@@@@@@@@@@@@@@@@;!;;;!;!;!@@@@@@@@@,,,@@@,@@@@,@@@@,,@@@@@@,@@@@@@-@@,,@@@@@@@@,@@@@@@,@@@@@@,,-@@@@@@@@@@@@@@@@
 *  @@@@@@;@@@@@@@@@@@@@@@@@@!!!!!!!@*@@@@@@,,,,@,@@@,@@@@,@@@@,,@@@@@@,@@@@@@@@@,,@@@@@@@@,@@@@@@,@@@@@,,,, @@@@@@@@@@@@@@@
 *  @@@@@@!@@@@@@@@@@@@@@@@@@@@@@@@@@;@@@@@@,@@@@,@@@,@@@,,@@@@,,@,,,,,,,,,,-@@@@,,@@@@@@@@,@@@@@@,@@@@,,@@,,,@@@@@@@@@@@@@@
 *
 ***************************************/




/***
 *
 * Basic setting
 *
 ***/

$pwd = "password"; //Set password
$access_token = "xxx"; // Set Api token

/* End of Setting */


$git_Data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']); // Get Git@osc data

if ($git_Data->password != $pwd) {       // password
    header('HTTP/1.0 404 Not Found');
    exit();
}

// Start to Get the Git@osc data analysis

$title = $hook_name = $git_Data->hook_name; // Get the action


/*
 * Function the action
 * Push  note merge_request issue tag
 *
 **/


// Push
function fnPush($data)
{
    // Get the Commit
    $commit_arr = $data->commits;
    $commit_messages = "";

    //decompose Commit
    foreach ($commit_arr as $commit) {

        // Processing character
        $commit->id = substr($commit->id, 0, 7);
        $commit->timestamp = date($commit->timestamp);

        // Add character
        $commit_messages .= <<<EOF
                NHL \n id： {$commit->id}
                NHL Time： {$commit_time}
                NHL >  {$commit->message}
                NHL -------
EOF;
    }

    $messages = <<< EOF
            \n ## {$data->repository->name}   Push 提示
            \n 分支：{$data->ref} 
            \n 提交者：[{$data->user_name}]({$data->user->url})
            NHL 共{$data->total_commits_count}个 Commit
            NHL #### Commit 详情
             {$commit_messages}
            NHL #### 仓库
            NHL 仓库名称：{$data->repository->name}
            NHL > {$data->repository->description}
            NHL \n [仓库地址]( {$data->repository->url} )
            NHL \n  [仓库主页]( {$data->repository->homepage} )
EOF;
    return $messages;
}

// Note
function fn_Note($data)
{
    $Note_msg= str_replace(array("\r\n", "\r"), 'NHL', $data->note);
    $messages = <<<EOF
       \n  ##  {$data->project->name} {$data->noteable_type} 提示
        NHL Id： {$data->noteable_id}
        NHL 作者：{$data->author->user_name}
        NHL --内容--
        NHL  {$Note_msg}
        NHL --内容--
        NHL #### 仓库
        NHL 仓库名称：{$data->repository->name} 
        NHL [仓库链接]({$data->repository->url})
        NHL 仓库描述：
        NHL > {$data->repository->description}
EOF;
    return $messages;
}

// PullRequest
function fnMerge_request($data)
{
    $Merge_request_body= str_replace(array("\r\n", "\r"), 'NHL', $data->body);
    $messages = <<<EOF
        \n  ##  {$data->source_repo->repository->name} PullRequest 提示
        \n iid： #{$data->iid} 
        NHL 标题： {$data->title} 
        NHL --说明---
        NHL  {$Merge_request_body} 
        NHL \n --说明---
        NHL \n 状态： {$data->state} 
        NHL 合并状态： {$data->merge_status} 
        NHL 提起者：{$data->author->user_name} 
        NHL > {$data->source_repo->project->path_with_namespace} : {$data->source_branch}
        NHL ↓↓↓↓↓↓↓↓↓↓↓
        NHL  {$data->target_repo->project->path_with_namespace} : {$data->target_branch}

        NHL ### 源项目
        NHL 项目名称：{$data->source_repo->project->name}
        NHL > {$data->source_repo->project->description}
        NHL [项目地址]({$data->source_repo->project->url})
        NHL ### 源仓库
        NHL 仓库名称：{$data->source_repo->repository->name}
        NHL > {$data->source_repo->repository->description}
        NHL \n [仓库链接]({$data->source_repo->repository->url})
        NHL ### 目标项目
        NHL 目标项目名称：{$data->target_repo->project->name}
        NHL > {$data->target_repo->project->description}
        NHL \n [目标项目地址]({$data->target_repo->project->url})
        NHL ### 目标仓库
        NHL 目标仓库名称：{$data->target_repo->repository->name}
        NHL > {$data->target_repo->repository->description}
        NHL \n [目标仓库链接]({$data->target_repo->repository->url})

EOF;
    return $messages;

}

/* Issue */
function fn_Issue($data)
{
    $assignee=empty($data->assignee) ? "未指定":$data->assignee->user_name;
    $milestone=empty($data->milestone) ? "没有里程碑":$data->milestone;
    $Issue_request_body= str_replace(array("\r\n", "\r"), 'NHL', $data->description);
    $messages = <<<EOF
        \n ## {$data->project->name} Issue 提示
        NHL iid： {$data->iid}
        NHL 名称：{$data->title} 
        NHL --描述--
        NHL {$Issue_request_body} 
        NHL --描述--
        NHL 状态：{$data->state}
        NHL 指定： {$assignee} 
        NHL 里程碑：{$milestone}
        NHL 提起者：{$data->user->username} 

        NHL ### 项目
        NHL 项目名称：{$data->project->name}
        NHL 项目分支：{$data->project->default_branch}
        NHL [项目地址]({$data->project->url})

        NHL ### 仓库
        NHL 仓库名称：{$data->repository->name}
        NHL [仓库链接]({$data->repository->url})
        NHL > {$data->repository->description}

EOF;


    return $messages;
}

/*Tag*/
function fn_Tag($data)
{

    $messages = <<<EOF
        \n ## {$data->project->name} 新增 Tag  提示
        NHL ref：{$data->ref} 
        NHL Before
        NHL > {$data->before}
        NHL \n After
        NHL > {$data->after}
        NHL ### 项目
        NHL 项目名称：{$data->project->name}
        NHL [项目地址]({$data->project->url})
        NHL 项目分支：{$data->project->default_branch}
        NHL ### 仓库
        NHL 名称：{$data->repository->name}
        NHL [链接]({$data->repository->url})
        NHL > {$data->repository->description}
EOF;
    return $messages;
}




switch ($hook_name) {
    case "push_hooks":
        $messages = fnPush($git_Data);
        $title = $git_Data->repository->name ." Push";
        break;
    case "note_hooks":
        $messages = fn_Note($git_Data);
        $title = $git_Data->project->name ." ". $git_Data->noteable_type;
        break;
    case "merge_request_hooks":
        $messages = fnMerge_request($git_Data);
        $title = $git_Data->source_repo->repository->name . " PullRequest";
        break;
    case "issue_hooks":
        $messages = fn_Issue($git_Data);
        $title = $git_Data->project->name . " issue";
        break;
    case "tag_push_hooks":
        $messages = fn_Tag($git_Data);
        $title = $git_Data->project->name . " New Tag";
        break;

    default:
        $messages = "Error ! The ip is" . $_SERVER["REMOTE_ADDR"];
        break;
}


/******
 *
 *
 *  Happy !!!!!!!!!!!!
 *  By keac
 *
 * */
$messages .= <<<EOF
    NHL &nbsp;&nbsp;
    NHL 工作累了，记得休息哦~
EOF;

$messages = str_replace(array("\r\n", "\r"), " ", $messages);
//$messages = str_replace(array(" "), 'k', $messages);
$messages = str_replace("NHL", " &nbsp;&nbsp; \n\n", $messages);



/*************
 *
 *  To DingTalk
 *
 ************/


//    $isAtAll = false;
//$data = array('msgtype' => 'text', 'text' => array('content' => $messages,), 'at' => array('atMobiles' => array(), 'isAtAll' => $isAtAll,),);


$data = array('msgtype' => 'markdown', 'markdown' => array('title' => $title, 'text' => $messages,),);


curlDing($data, $access_token);



/*
 *
 * Post to DingTalk
 *
 * */


function curlDing($data, $access_token){


    $data_string = json_encode($data); // To json
    $urls = "https://oapi.dingtalk.com/robot/send?access_token=" . $access_token; // To DingTalk API
    /* POST */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
    curl_setopt($ch, CURLOPT_URL, "$urls");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    $retBase = curl_exec($ch);
    curl_close($ch);

    /* End of Post */

    return $retBase;
}