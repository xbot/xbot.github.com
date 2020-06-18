# 简化Vim Addons Manager安装扩展的操作


相对Sublime的Package Control，VAM安装和卸载扩展的操作很烦琐。以下脚本在安装完扩展后自动注册，无须手工添加到vimrc，另外增加命令:UninstallAddons，从注册表中删除扩展。

```vim
set runtimepath+=~/.vim/addons/vim-addon-manager
let active_addons = []
let s:vamRegistryFile = expand('~').'/.vim/vam_registry'
if filereadable(s:vamRegistryFile)
    let active_addons += readfile(s:vamRegistryFile)
endif
call vam#ActivateAddons(active_addons)
" Addon post-install hook.
fun! MyAddonPostActivateHook(info, repository, pluginDir, opts)
    if filereadable(s:vamRegistryFile)
        let regLines = readfile(s:vamRegistryFile)
        call add(regLines, a:repository.name)
        call sort(regLines)
        call uniq(regLines)
    else
        let regLines = [a:repository.name]
    endif
    call writefile(regLines, s:vamRegistryFile)
endfun
let g:vim_addon_manager.post_install_hook_functions = ['MyAddonPostActivateHook']
" Complete the addon name.
fun! MyDoActivatedAddonsCompete(...)
    let fullList = keys(g:vim_addon_manager.activated_plugins)
    call filter(fullList, 'v:val =~ ".*'.a:1.'.*"')
    return fullList
endfun
" Remove records of addons from the registry.
fun! MyUninstallAddons(...)
    if filereadable(s:vamRegistryFile)
        let regLines = readfile(s:vamRegistryFile)
    else
        echo "Registry is empty."
        return
    endif
    for addonName in a:000
        let idx = index(regLines, addonName)
        if idx >= 0
            call remove(regLines, idx)
            echo addonName.' is removed from the registry.'
        else
            echo addonName.' cannot be found in the registry.'
        endif
    endfor
    call writefile(regLines, s:vamRegistryFile)
endfun
command! -complete=customlist,MyDoActivatedAddonsCompete -nargs=* UninstallAddons :call MyUninstallAddons(<f-args>)
```

