#!/bin/sh

hexo clean
hexo g

find public -name "*.html" |xargs sed -i '' "s#/styles/crisp.css#$HOME/Projects/blog/public/styles/crisp.css#g"
find public -name "*.html"|xargs font-spider

cp -f public/fonts/FZBeiWeiKaiShu.ttf themes/crisp/source/fonts/FZBeiWeiKaiShu.ttf

hexo clean
hexo s -g