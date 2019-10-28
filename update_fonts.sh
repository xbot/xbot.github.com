#!/bin/sh

hexo clean
hexo g

# find public -name *.css | xargs sed -i '' 's#FZBeiWeiKaiShu.ttf#FZBeiWeiKaiShu-full.ttf#g'
# find public -name "*.html" |xargs sed -i '' "s#/styles/crisp.css#$HOME/Projects/blog/public/styles/crisp.css#g"
# find public -name "*.html"|xargs font-spider

# cp -f public/fonts/FZBeiWeiKaiShu-full.ttf themes/crisp/source/fonts/FZBeiWeiKaiShu.ttf

find public -name *.css | xargs sed -i '' 's#FZBeiWeiKaiShu.ttf#FZBeiWeiKaiShu-full.ttf#g'
find public -name "*.html" |xargs sed -i '' "s#/css/style.css#$HOME/Projects/blog/public/css/style.css#g"
find public -name "*.html"|xargs font-spider

cp -f public/webfonts/FZBeiWeiKaiShu-full.ttf themes/blairos/source/webfonts/FZBeiWeiKaiShu.ttf

hexo clean
hexo s -g
