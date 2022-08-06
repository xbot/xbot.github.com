---
title: "Laravel Horizon 简介"
slug: "an Introduction of Laravel Horizon"
date: 2022-08-06T20:22:36+08:00
categories:
- 计算机
tags:
- 编程
- Laravel
- PHP
---

## 简介

Horizon 针对 Laravel 的 Redis 队列，增加了可视化、进程池等特性。

## 特性

### 可视化仪表板

![2022-08-06-20-27-48-xu94E4](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-27-48-xu94E4.jpg)

### Worker 进程池的维护和调度

#### 词汇表

- master supervisor: 主进程，通过 `proc_open()` 启动 supervisor 子进程。
- environments: 可以按 `APP_ENV` 针对不同环境创建多套配置。
- supervisors: Horizon 自己的进程池概念，与托管后台进程的软件 Supervisor 没有关系。
- workers: 队列消费者进程，每个 supervisor 中包含多个 worker。

#### 负载均衡策略

##### false

一个 supervisor 下的所有 worker 全部用于按队列名称的顺序逐个队列消费任务。

##### simple

一个 supervisor 下的所有 worker 被平均分配给每个队列。

##### auto

一个 supervisor 下空闲的 worker 会被优先分配给负载最高的队列。同时保证空闲队列有配置项 minProcesses 数量的 worker 待命。

### 超时提醒

如果一个队列执行任务的时间超过预先配置的时间限制，horizon 将终止该任务并可以通过短信、邮件或 Slack 发送通知。

Horizon 每分钟查询一次执行任务的时间超过配置值的队列：

![2022-08-06-20-30-29-t2RJyM](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-30-29-t2RJyM.jpg)

每 5 分钟发送一次通知：

![2022-08-06-20-31-03-USbLPG](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-31-03-USbLPG.jpg)

### 吞吐量和平均耗时统计

需要通过定时任务周期性执行 `artisan horizon:snapshot` 命令生成统计数据并存储到 Redis 中。

```php
$schedule->command('horizon:snapshot')->everyFiveMinutes()
```

按 Job 查看统计图表：

![2022-08-06-20-31-30-lW3rbM](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-31-30-lW3rbM.jpg)

### 标签

如果任务中包含了 Eloquent model, Horizon 会自动使用这个 model 的 ID 给任务打标签。也可以通过覆盖 tags() 方法自己定义：

```php
public function tags(): array
{
    return ['tests', 'test:' . $this->id];
}
```

通过标签监控和查看任务的执行情况：

![2022-08-06-20-31-59-9fuSot](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-31-59-9fuSot.jpg)

## 问题

### Horizon 不消费队列

#### 问题的表现

- `artisan horizon` 启动后不消费队列
- dashboard 中看不到 supervisor-1
    ![2022-08-06-20-32-36-suv4Se](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-32-36-suv4Se.jpg)
- `artisan horizon:list` 可以看到 supervisor-1
    ![2022-08-06-20-34-22-Rkz4kE](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-34-22-Rkz4kE.jpg)
- `artisan horizon:supervisors` 查不到 supervisor-1
    ![2022-08-06-20-35-05-2s5FVk](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-35-05-2s5FVk.jpg)
- Redis 中 Horizon 的元数据存储在两个目录中
    ![2022-08-06-20-35-32-lfcXqI](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-35-32-lfcXqI.jpg)

### 原因

Laravel 在 `.env` 之外会根据 `APP_ENV` 加载对应环境的 dotenv 文件：

![2022-08-06-20-35-56-fWubbM](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-08-06-20-35-56-fWubbM.jpg)

Horizon 先启动 master 进程，之后 master 启动 supervisor 进程。

在 master 启动时，由于 `.env` 文件还没有被加载，所以 `APP_ENV` 为空，导致 `.env.local` 没有被加载。由于 `.env` 中没有设置 `APP_NAME` ，所以 Horizon 在取 prefix 的时候得到了默认值 `laravel` 。

在 supervisor 进程被启动时，由于 master 进程已经加载了 `.env` 文件，所以 `.env.local` 被加载。由于 `.env.local` 中设置了 `APP_NAME` ，所以 Horizon 在取 prefix 的时候得到了配置值 `retail` 。

所以 master 进程的元数据被存储到 `laravel_horizon` 下，supervisor 进程的元数据被存储到 `retail_horizon` 下。

### 解决

- .env 中设置 APP_NAME
- `artisan horizon --env local`
