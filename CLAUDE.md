# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Hugo-based static blog ("晴耕雨讀") hosted at 0x3f.org. 550+ posts spanning 2007–2026 in Chinese. Deployed to GitHub Pages via GitHub Actions.

## Build & Development Commands

```bash
# Local development server
hugo server

# Production build (matches CI)
hugo --minify

# Create a new post (Hugo archetype auto-generates title/slug from filename)
hugo new posts/2026-02-12-my-post-slug.md
```

Build output goes to `./public/`. The site requires Hugo extended version (for SCSS).

## Deployment

- **Source branch:** `hugo` (where edits happen)
- **Published branch:** `master` (GitHub Pages serves from here)
- Pushing to `hugo` triggers GitHub Actions (`.github/workflows/main.yml`) which builds and deploys automatically
- The theme (`themes/minima`) is a git submodule pointing to `xbot/hugo-theme-minima`

## Architecture

- **Config:** `config.yaml` — single Hugo config file (YAML format)
- **Content:** `content/posts/` — blog posts named `YYYY-MM-DD-slug.md` (or `.html` for legacy)
- **Theme:** `themes/minima/` — forked from mivinci/hugo-theme-minima, customized. Two other themes (LoveIt, ivy) are present as submodules but inactive
- **Custom CSS:** `assets/css/main.scss` — imports LXGW WenKai font and theme styles
- **Static assets:** `static/` — images, favicons, CNAME, PWA manifest
- **Archetype:** `archetypes/default.md` — strips date prefix from filename to derive title and slug automatically
- **Obsidian integration:** `scripts/create_new_post.js` is a QuickAdd script for creating posts from Obsidian

## Post Frontmatter Format

```yaml
---
title: "Post Title"
slug: "post-slug"
date: 2026-02-12T10:00:00+08:00
draft: false
tags:
  - tag1
categories:
  - 计算机       # or 青梅煮酒, 行见
series:
  - series-name  # optional
---
```

## Key Configuration Details

- Language: `zh-cn` (Chinese Simplified)
- Comments: Utterances (GitHub issues in `xbot/blog-comments`)
- Search: Fuse.js (client-side), index built from JSON output
- Math: KaTeX enabled
- Diagrams: Mermaid enabled
- Markdown: Goldmark with `unsafe: true` (allows raw HTML in posts)
- Taxonomy types: categories, tags, series
