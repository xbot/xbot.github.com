#!/usr/bin/env php
<?php

if ($argc < 2) {
    file_put_contents('php://stderr', "需输入一个合法的文件路径\n");
    exit(1);
}

for ($i=1; $i < $argc; $i++) {
    handle_file($argv[$i]);
}

function handle_file($filePath)
{
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);

    $isDateMissing = empty(preg_grep('/^date:\s*/', $lines));
    if ($isDateMissing) {
        append_date($filePath, $lines);
    }

    $isSlugMissing = empty(preg_grep('/^slug:\s/', $lines));
    if ($isSlugMissing) {
        append_slug($filePath, $lines);
    }

    convert_hexo_tags($lines);

    file_put_contents($filePath, implode("\n", $lines));
}

function append_date($filePath, &$lines)
{
    // 获取文件名中的日期
    $arr = [];
    preg_match('/\d{4}-\d{2}-\d{2}/', basename($filePath), $arr);
    if (empty($arr)) {
        file_put_contents('php://stderr', "未能从文件名中找到日期\n");
        exit(1);
    }

    foreach ($lines as $idx => $line) {
        if (strpos($line, 'title:') === 0) {
            $lines = array_merge(
                array_slice($lines, 0, $idx + 1),
                ["date: {$arr[0]} 00:00:00"],
                array_slice($lines, $idx + 1)
            );
            break;
        }
    }
}

function append_slug($filePath, &$lines)
{
    if (! preg_match('/(?<=\d{4}-\d{2}-\d{2}-).*(?=\.)/', basename($filePath), $matches)) {
        file_put_contents('php://stderr', "未能从文件名中找到slug\n");
        exit(1);
    }

    foreach ($lines as $idx => $line) {
        if (strpos($line, 'title:') === 0) {
            $lines = array_merge(
                array_slice($lines, 0, $idx + 1),
                ["slug: " . str_replace('-', ' ', $matches[0])],
                array_slice($lines, $idx + 1)
            );
            break;
        }
    }
}

function convert_hexo_tags(&$lines)
{
    foreach ($lines as $idx => $line) {
        if (preg_match('/(?<={%\simg\s).*(?=\s\d+\s%})/', $line, $matches)
            || preg_match('/(?<={%\simg\s).*(?=\s%})/', $line, $matches)
        ) {
            $lines[$idx] = "![]({$matches[0]})";
        }

        if (preg_match('/(?<={%\scodeblock\slang:).*(?=\s[a-zA-Z0-9.]+\s%})/', $line, $matches)
            || preg_match('/(?<={%\scodeblock\slang:).*(?=\s%})/', $line, $matches)
            || preg_match('/(?<={%\scodeblock).*(?=\s%})/', $line, $matches)
        ) {
            $lines[$idx] = "```{$matches[0]}";
        }

        if (preg_match('/{%\sendcodeblock\s%}/', $line)) {
            $lines[$idx] = "```";
        }
    }
}
