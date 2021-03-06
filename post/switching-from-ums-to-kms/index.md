# 被迫弃UMS而用KMS

<p>昨天更新了一下系统，今天开机就白屏了。于是先关掉Compiz，看了一下Archlinux的<a href="http://www.archlinux.org/news/484/">新闻</a>，发现新的<strong>xf86-video-intel</strong>中已经移除了UMS，也就意味着只能使用KMS做3D加速了。</p>

<p><a href="http://www.thinkwiki.org/wiki/Intel_Graphics_Media_Accelerator_950#User_mode_setting_.28UMS.29">UMS</a>的全称是<strong>User Mode-Setting</strong>，是一种传统的图形界面初始化方案，即在X加载之后由它初始化图形界面。这种方案的弊端是虚拟终端不具备显示和处理图形的能力，同时虚拟终端和图形界面之间的切换显得缓慢并带有闪烁。</p>

<p><a href="http://wiki.archlinux.org/index.php/KMS">KMS</a>的全称是<strong>Kernel Mode-Setting</strong>，是新一代图形界面初始化方案，它将图形界面的初始化由X加载之后由X负责进行改为在内核初始化时由内核进行。KMS的好处不仅仅是解决了上面UMS的问题，同时也使得Linux具备了在启动时显示漂亮的开机图示的能力。另外，在3D加速性能和低功耗方面，KMS也较UMS更胜一筹。</p>

<p>我的<a href="http://0x3f.org/?p=819">Thinkpad X200</a>使用的是Intel GMA 945的芯片组，而Archlinux的<a href="http://wiki.archlinux.org/index.php/Intel_(简体中文)#KMS_.28Kernel_Mode_Setting.29">Wiki</a>上仍以915为例，所以尝试着做如下内容：</p>

<p>一、去除/boot/grub/menu.lst中Kernel启动参数中的<a href="http://0x3f.org/?p=866">vga参数</a>；</p>

<p>二、加入以下内容到/etc/modprobe.d/modprobe.conf：</p>

<blockquote>
  <p>options i945 modeset=1</p>
</blockquote>

<p>三、在/etc/rc.conf中的<strong>MODULES</strong>行加入<strong>intel_agp</strong>和<strong>i945</strong>；</p>

<p>重启系统后，Compiz白屏问题解决，3D加速性能似乎有所上升。</p>

