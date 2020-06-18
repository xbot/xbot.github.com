# InstallShield在MySQL和Oracle中执行SQL脚本的方法

<h2>简述</h2>

InstallShield已经内建了对MySQL和Oracle的支持。但是这个功能是通过ODBC实现的，它对SQL脚本的格式要求非常严格，因此已经通过官方客户端测试的脚本在IS中执行时往往就会报错。

一般来说，数据库脚本只保证通过官方客户端测试即可，同时维护一份供IS执行的脚本费时费力。因此，考虑安装程序对两数据库的支持通过官方客户端实现。

<h2>MySQL</h2><br>

```
function InstallMySQLComponent(szComponent)
    NUMBER nResult;
    STRING szServer,szDB,szUser,szPassword,sCMD,sOPT,sResult1,sResult2,svLine,sMsg,sPath;
    NUMBER nvFileHandle,nvCount;
    LIST listStatus;
begin
    sMsg = '安装'+szComponent+' ...';
    SdShowMsg(sMsg, TRUE);
    // source命令不认识windows路径中的反斜杠，故将SRCDIR中的反斜杠替换成斜杠
    sPath = SRCDIR;
    StrReplace(sPath, '\\', '/', 0);
    // Fetch database connection information
    SQLRTGetConnectionInfo( 'mysql', szServer, szDB, szUser, szPassword );
    sCMD = WINSYSDIR^'cmd.exe';
    sOPT = ' /c '+SRCDIR^'mysql.exe -h'+szServer+' -u'+szUser+' -p'+szPassword+' -D'+szDB;
    sOPT = sOPT+' -e "source '+sPath^szComponent+'.sql" > '+SRCDIR^'dbstatus.txt 2>&1';
    // Execute the script associated with the given component in database
    nResult=LaunchAppAndWait(sCMD, sOPT, WAIT|LAAW_OPTION_HIDDEN);
    if (nResult < 0) then
        MessageBox('Failed installing '+szComponent+' !', SEVERE);
        abort;
    endif;
    // 关闭安装提示
    SdShowMsg('', FALSE);
    // Read dbstatus.txt
    OpenFileMode(FILE_MODE_NORMAL);
    if (OpenFile(nvFileHandle, SRCDIR, 'dbstatus.txt')<0) then
        MessageBox('Failed checking the status of installing '+szComponent+' !', SEVERE);
        abort;
    endif;
    listStatus = ListCreate(STRINGLIST);
    while GetLine(nvFileHandle, svLine) = 0
        ListAddString(listStatus, svLine, AFTER);
    endwhile;
    CloseFile(nvFileHandle);
    // Count how many lines fetched from dbstatus.txt
    nvCount = ListCount(listStatus);
    if nvCount > 0 then
        sMsg = "更新数据库出错，点“是”打开日志文件并退出安装，点“否”直接退出安装。\n";
        sMsg = sMsg+"若错误可忽略，可选择数据库类型“none”以跳过数据库更新并直接更新程序，\n";
        sMsg = sMsg+"然后在数据库中手工执行SQL脚本（安装后保存在script目录下）";
        nResult = AskYesNo(sMsg, YES);
        if (nResult = YES) then
            LaunchApp(WINSYSDIR^'notepad.exe', SRCDIR^'dbstatus.txt');
	endif;                   
        abort;
    endif;
end;
```

<h2>Oracle</h2><br>

```
function InstallOracleComponent(szComponent)  
    NUMBER nResult,nvFileHandle,nIndex,nvCount;
    STRING sMsg,szServer,szDB,szUser,szPassword,sCMD,sOPT,sInstance,sTmp,svLine;
    LIST listStatus;
begin
    sMsg = '安装'+szComponent+' ...';
    SdShowMsg(sMsg, TRUE);
    // Fetch database connection information
    SQLRTGetConnectionInfo( 'oracle', szServer, szDB, szUser, szPassword );
    nIndex = StrFind(szServer, ':');
    nIndex = StrFindEx(szServer, '/', nIndex);
    StrSub(sInstance, szServer, nIndex+1, 100);
    sCMD = WINSYSDIR^'cmd.exe';
    sOPT = ' /c '+'sqlplus.exe -L -S '+szUser+'/'+szPassword+'@'+sInstance;
    sOPT = sOPT+' @'+SRCDIR^szComponent+'.sql > '+SRCDIR^'dbstatus.txt 2>&1';
    // Execute the script associated with the given component in database
    nResult=LaunchAppAndWait(sCMD, sOPT, WAIT|LAAW_OPTION_HIDDEN);
    if (nResult < 0) then
        MessageBox('Failed installing '+szComponent+' !', SEVERE);
        abort;
    endif;               
    // 关闭安装提示
    SdShowMsg('', FALSE);
    // 在dbstatus.txt中查询字符串holytail，如果存在，说明脚本已执行完
    if (FileGrep(SRCDIR^'dbstatus.txt', 'holytail', svLine, nIndex, RESTART) = 0) then
        // 在dbstatus.txt中查询字符串ORA-，如果存在，说明脚本执行出现错误
        if (FileGrep(SRCDIR^'dbstatus.txt', 'ORA-', svLine, nIndex, RESTART) = 0) then
            sMsg = "更新数据库出错，点“是”打开日志文件并退出安装，点“否”直接退出安装。\n";
            sMsg = sMsg+"若错误可忽略，可选择数据库类型“none”以跳过数据库更新并直接更新程序，\n";
            sMsg = sMsg+"然后在数据库中手工执行SQL脚本（安装后保存在script目录下）";
            nResult = AskYesNo(sMsg, YES);
            if (nResult = YES) then
                LaunchApp(WINSYSDIR^'notepad.exe', SRCDIR^'dbstatus.txt');
            endif;                   
            abort;
        endif;
    else
        sMsg = "更新数据库出错，点“是”打开日志文件并退出安装，点“否”直接退出安装。\n";
        sMsg = sMsg+"若错误可忽略，可选择数据库类型“none”以跳过数据库更新并直接更新程序，\n";
        sMsg = sMsg+"然后在数据库中手工执行SQL脚本（安装后保存在script目录下）";
        nResult = AskYesNo(sMsg, YES);
        if (nResult = YES) then
            LaunchApp(WINSYSDIR^'notepad.exe', SRCDIR^'dbstatus.txt');
        endif;                   
        abort;
    endif;
end;
```

<h2>总结</h2>

<ol>
	<li>为便于获取脚本在数据库中的执行结果，故通过官方客户端执行脚本时通过符号“<strong>></strong>”将客户端的输出信息重定向到<strong>dbstatus.txt</strong>中；同时，使用“<strong>2>&1</strong>”将标准错误输出重定向到标准输出设备上，当然，会进一步重定向到dbstatus.txt文件中，否则，无法获取出错信息。</li>
	<li>sqlplus执行SQL脚本后不会自动退出，故应在Oracle的脚本后加上语句“<strong>exit;</strong>”。</li>
	<li>重载<strong>OnSQLComponentInstalled()</strong>函数，并在其中禁止MySQL和Oracle的SQL脚本对应的Component被执行安装，然后通过以上两个函数更新数据库。</li>
</ol>

