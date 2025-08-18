{{- $raw := .BaseFileName -}}
{{- $base := replaceRE `^\d{4}-\d{2}-\d{2}-` "" $raw -}}
{{- $title := title (replace $base "-" " ") -}}
{{- $slug := urlize $base -}}
---
title: {{ $title | jsonify }}
slug: {{ $slug | jsonify }}
date: {{ .Date }}
draft: false
---

