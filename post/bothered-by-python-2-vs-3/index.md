# Python 2还是3是个纠结的问题


前些天用denite取代unite，用`--with-python3`重装了vim，结果发现vdebug只支持python 2。

vim同时只能在py2和py3中选一个，据说可以通过重新编译python实现共存，但是我在编译py3的时候失败。

neovim同时支持两个版本，但是尝试deoplete失败，报「Invalid Channel」的错误，似乎是python-neovim的锅。而且由于neovim内建lua、不支持`has('lua')`，所以不能fall back到neocomplete。

万般无奈，只好换回unite。


