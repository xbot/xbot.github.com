# 用gv.vim查看git提交历史

[gv.vim](https://github.com/junegunn/gv.vim)是fugitive的插件，用于查看git提交历史，特点是速度快、好用。我现在用它做code review。

```vim
nnoremap <leader>gll :GV --no-merges<CR>
nnoremap <leader>glc :GV!<CR>
nnoremap <leader>gla :GV --no-merges --author<space>
nnoremap <leader>glg :GV --no-merges --grep<space>
```


