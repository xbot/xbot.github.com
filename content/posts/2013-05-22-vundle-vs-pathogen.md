---
layout: post
title: Vundle vs Pathogen
slug: vundle vs pathogen
date: 2013-05-22 21:33:00
comments: true
tags:
- Vim
- 计算机
---
I have been using Pathogen for a long time and I am happy with it. But it seems that more and more Vim scripts are recommending to use Vundle in their install references. So I made a study today, the conclusion is, although almost all the articles I found from Google have a positive attitude on Vundle, I still prefer Pathogen.

Most supporters of Vundle praise it for one reason, that is, it can install and update scripts automatically, which is lacked by Pathogen. Yes, since Vundle leverages the vim-scripts repository on GitHub, it is really easier to do so than Pathogen. But the disadvantage is as good as the advantage. I found that the vim-scripts repo is not updated in time, the latest activity was carried out a month ago ! As an OCD patient of updating, I cannot tolerate old versions of vim scripts. Although that Vundle supports using scripts' git repos of their own (either on GitHub or other places), there is still a problem, what if authors commit broken code to the master brunch ?

Moreover, not all scripts are deposited in public git repos. How can I organize those scripts in ~/.vim/bundle/ and prevent :BundleClean from deleting them at the same time ?

Pathogen only handles the organization job that puts scripts in individual folders under ~/.vim/bundle/, so there is without the above problem. For updating issues, GetLatestVimScripts.vim is a good choice, it fetches the latest stable updates from vim.org, so I don't have to worry about careless commits. The only problem is, when GetLatestVimScripts.vim downloads all packages, I should install them manually. But since most scripts rarely update, I think this is not a hard work to do.

KISS things are always good.
