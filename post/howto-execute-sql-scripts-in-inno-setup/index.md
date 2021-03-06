# Inno Setup执行SQL脚本的方法

<p>作为和<a href="http://zh.wikipedia.org/zh-cn/Nullsoft%E8%85%B3%E6%9C%AC%E5%AE%89%E8%A3%9D%E7%B3%BB%E7%B5%B1">NSIS</a>并立的、两个最流行的免费Windows应用程序安装包制作工具之一，<a href="http://en.wikipedia.org/wiki/Inno_Setup">Inno</a>在学习难度上相对要低一些，非常适合对一些简单的桌面程序打包。但对于较复杂的安装过程，或者Web应用程序来说，我个人觉得不是Inno的强项。当然，既然Inno内嵌了<a href="http://zh.wikipedia.org/zh-cn/Pascal">Pascal</a>语言用以扩展功能，理论上不是不可以应付复杂的安装过程，但实现起来要复杂一些。</p>

<p>比如对于在安装过程中连接数据库并执行SQL脚本这样的需求，使用<a href="http://en.wikipedia.org/wiki/InstallShield">InstallShield</a>应该会简单地多，而Inno却不支持直接操作数据库，并且相关的资料说明少之又少，还不如NSIS丰富，以至于我踏破铁鞋无觅处，最终却在NSIS的资料中找到了思路。</p>

<p>主要的思路是，在安装过程中，调用数据库客户端连接数据库并执行SQL脚本，然后将执行结果或错误信息输出到文件中，最后通过分析这个文件来判断命令执行的结果。但是，既然是调用特定的客户端，那么对不同数据库的操作自然就有所区别，具体情况如下所述。</p>

<p>首先在打包脚本的<strong>[Files]</strong>段将必需的文件包含进来：</p>

```ini
[Files]
Source: "D:\Development\MyDemoApp\code\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "D:\Development\MyDemoApp\sqlcmd.exe"; Flags: dontcopy
Source: "D:\Development\MyDemoApp\sqlcmd.rll"; Flags: dontcopy
Source: "D:\Development\MyDemoApp\mysql.exe"; Flags: dontcopy
Source: "D:\Development\MyDemoApp\script_mssql.sql"; Flags: dontcopy
Source: "D:\Development\MyDemoApp\script_mysql.sql"; Flags: dontcopy
Source: "D:\Development\MyDemoApp\script_oracle.sql"; Flags: dontcopy
```

<p>在SQL Server中执行脚本的代码片断：</p>

```pascal
function ExecScriptInMSSQL(DBHost, DBLogin, DBPass, DBName: String): Boolean;
var
    ConnectExe: String;
    ConnectParam: String;
begin
    {解压临时文件}
    ExtractTemporaryFile('sqlcmd.exe');
    ExtractTemporaryFile('sqlcmd.rll');
    ExtractTemporaryFile('script_mssql.sql');
    {构造数据库连接字符串}
    ConnectExe := ExpandConstant('{tmp}') + '\sqlcmd.exe';
    ConnectParam := ' -S ' + DBHost
        + ' -U ' + DBLogin
        + ' -P ' + DBPass
        + ' -d ' + DBName
        + ' -i script_mssql.sql -o '
        + ExpandConstant('{tmp}') + '\dbstatus.txt';
    {建立数据库连接并执行脚本}
    if Exec(ConnectExe, ConnectParam, '', SW_HIDE, ewWaitUntilTerminated, ResultCode) then begin
        Result := ResultCode = 0;
        LoadStringFromFile(ExpandConstant('{tmp}') + '\dbstatus.txt', StatusString);
        if StatusString <> '' then begin
            MsgBox(StatusString, mbError, MB_OK);
            Result := False;
        end else begin
            Result := True;
        end;
    end else begin
        MsgBox('Database update failed:'#10#10 + SysErrorMessage(ResultCode), mbError, MB_OK);
        Result := False;
    end;
end;
```

<p>在MySQL中执行脚本的代码片断：</p>

```pascal
function ExecScriptInMYSQL(DBHost, DBLogin, DBPass, DBName: String): Boolean;
var
    ConnectExe: String;
    ConnectParam: String;
begin
    {解压临时文件}
    ExtractTemporaryFile('mysql.exe');
    ExtractTemporaryFile('script_mysql.sql');
    {构造数据库连接字符串}
    ConnectExe := ExpandConstant('cmd');
    ConnectParam := ' /c "' + ExpandConstant('{tmp}') + '\mysql.exe'
        + ' -h' + DBHost
        + ' -u' + DBLogin
        + ' -p' + DBPass
        + ' -D' + DBName
        + ' -e "source ' + ExpandConstant('{tmp}') + '\script_mysql.sql"" > ' + ExpandConstant('{tmp}') + '\dbstatus.txt 2>&1';
    {建立数据库连接并执行脚本}
    if Exec(ConnectExe, ConnectParam, '', SW_HIDE, ewWaitUntilTerminated, ResultCode) then begin
        Result := ResultCode = 0;
        LoadStringFromFile(ExpandConstant('{tmp}') + '\dbstatus.txt', StatusString);
        if StatusString <> '' then begin
            MsgBox(StatusString, mbError, MB_OK);
            Result := False;
        end else begin
            Result := True;
        end;
    end else begin
        MsgBox('Database update failed:'#10#10 + SysErrorMessage(ResultCode), mbError, MB_OK);
        Result := False;
    end;
end;
```

<p>由于mysql.exe没有输出结果到文件的参数，故需要使用cmd.exe来运行mysql.exe以便将其输出重定向到文件dbstatus.txt中。此外，在命令的最后加上参数<strong>2&gt;&amp;1</strong>，将标准错误输出设备也重定向到文件上，否则命令执行的错误信息不会输出到文件中。</p>

<p>在Oracle中执行脚本的代码片断：</p>

```pascal
function ExecScriptInORACLE(ClientPath, DBInstance, DBLogin, DBPass: String): Boolean;
begin
    {解压临时文件}
    ExtractTemporaryFile('script_oracle.sql');
    {连接数据库并执行脚本}
    if Exec(ExpandConstant('cmd'), ' /c "' + ClientPath + ' -L -S ' + DBLogin
        + '/' + DBPass
        + '@' + DBInstance
        + ' @' + ExpandConstant('{tmp}') + '\script_oracle.sql > ' + ExpandConstant('{tmp}') + '\dbstatus.txt 2>&1',
        '',
        SW_HIDE, ewWaitUntilTerminated, ResultCode)
    then begin
        Result := ResultCode = 0;
        LoadStringFromFile(ExpandConstant('{tmp}') + '\dbstatus.txt', StatusString);
        if Pos('holytail', StatusString) <> 0 then begin
            {若输出信息中有“holytail”的子串，则表示脚本成功执行}
            {若执行有误，提示用户打开日志文件}
            if Pos('ORA-', StatusString) <> 0 then begin
                {提示用户脚本执行出错}
                if MsgBox('数据库更新出错，是否打开日志文件？', mbConfirmation, MB_YESNO) = IDYES then begin
                    {打开日志}
                    if not ShellExec('', ExpandConstant('{tmp}') + '\dbstatus.txt', '', '', SW_SHOW, ewNoWait, ErrorCode) then begin
                        MsgBox('日志文件打开错误！', mbError, MB_OK);
                    end;
                end;
                Result := False;
            {若执行无误，返回True}
            end else begin
                Result := True;
            end;
        end else if StatusString <> '' then begin
            MsgBox(StatusString, mbError, MB_OK);
            Result := False;
        end else begin
            Result := True;
        end;
    end else begin
        MsgBox('Database update failed:'#10#10 + SysErrorMessage(ResultCode), mbError, MB_OK);
        Result := False;
    end;
end;
```

<p>Oracle的客户端太大，不能集成到安装包中，应使用一个<strong>TInputFileWizardPage</strong>由用户选择sqlplus.exe的安装位置。同时，由于sqlplus.exe也没有输出结果到文件的参数，也须使用cmd.exe来运行它并重定向输出到文件。此外，由于sqlplus.exe执行脚本时无论成功还是失败，都会输出信息，故无法像使用sqlcmd.exe和mysql.exe那样简单地判断脚本是否执行成功，需要在脚本的最后通过select语句输出一个特殊的字符串到文件中，然后通过判断dbstatus.txt中是否存在该字符串来判断脚本的执行情况；且由于sqlplus.exe执行完脚本后不会自动退出，还要在脚本最后加上exit语句；故<strong>script_oracle.sql</strong>的最后必须是如下内容：</p>

```sql
SELECT 'holytail' FROM dual;
exit;
```

