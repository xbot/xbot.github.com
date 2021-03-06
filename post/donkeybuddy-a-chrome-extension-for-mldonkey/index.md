# DonkeyBuddy: A Chrome Extension for MLDonkey

<div class="illustration_left">
<a href="http://picasaweb.google.com/lh/photo/CLpE8Gtpb3r3F9kcqmS60g?feat=embedwebsite"><img src="http://lh5.ggpht.com/_ceUJ_lBTHzc/TJyWhIpMAgI/AAAAAAAABec/KDw6n_qdQ98/s800/donkey-256x256.png" /></a>
</div>

<p><strong>DonkeyBuddy</strong> is an extension for Google Chrome Browser. It's intended to make life easier when you add downloading tasks to MLDonkey.</p>

<h2>Features</h2>

<ol>
<li>Users can choose how to interact with MLDonkey, either by AJAX or
popup windows.</li>
<li>The AJAX mode interacts with MLDonkey by AJAX requests and uses
desktop notifications to show the results, so it won't bother you by
popping up windows and forcing you to close them.</li>
<li>The popup-window mode interacts with MLDonkey by popping up a window
and displays results in it, this is always a reliable way to add
downloads but annoying.</li>
<li>An icon will be displayed in the location bar only when downloadable
resources are found in the current tab, so it saves you the space in
both toolbar and location bar.</li>
<li>Batch downloading.</li>
</ol>

<h2>Screenshots</h2>

<ol>
<li>Location bar icon: <br />
<a href="http://picasaweb.google.com/lh/photo/021647BS--VLalgORMtuVA?feat=embedwebsite"><img src="http://lh6.ggpht.com/_ceUJ_lBTHzc/TJyWhUJIzBI/AAAAAAAABeg/ZQ2WeLfTf0k/s800/donkeybuddy-location_bar_icon_01.png" alt="image" /></a></li>
<li>Desktop notification: <br />
<a href="http://picasaweb.google.com/lh/photo/aBu0jSZu3y7Ux-EKKgQibQ?feat=embedwebsite"><img src="http://lh6.ggpht.com/_ceUJ_lBTHzc/TJyWhchc1lI/AAAAAAAABek/O3OuDRkgL88/s400/donkeybuddy-notification_01.png" alt="image" /></a></li>
<li>Popup window: <br />
<a href="http://picasaweb.google.com/lh/photo/GbGe38VG8mU_LyXZDFYgtA?feat=embedwebsite"><img src="http://lh5.ggpht.com/_ceUJ_lBTHzc/TJyWhmg-04I/AAAAAAAABeo/8cbj0a-m_Fg/s400/donkeybuddy-popup_window_01.png" alt="image" /></a></li>
<li>Settings page: <br />
<a href="http://picasaweb.google.com/lh/photo/uUKzfwcmEQfJaHY6cHVNqw?feat=embedwebsite"><img src="http://lh3.ggpht.com/_ceUJ_lBTHzc/TJyWhsEQILI/AAAAAAAABes/ezlPD6jaUuQ/s400/donkeybuddy-settings_01.png" alt="image" /></a></li>
</ol>

<h2>Install</h2>

<p><a href="https://chrome.google.com/webstore/detail/hbajjpcakkngealbehjippmdbfapodnn">https://chrome.google.com/webstore/detail/hbajjpcakkngealbehjippmdbfapodnn</a></p>

<h2>FAQ</h2>

<ol>
<li><p><strong>Why a success notification pops up when I haven't even started
MLDonkey ?</strong></p>

<ul>
<li>Since the responses of the cross-domain AJAX requests have an
empty message and a status which has the value 0, so I can't
identify that whether they are successful or not.</li>
<li>But when MLDonkey is not running, AJAX requests always take more
time to receive responses, so if a request has not received its
reponse within a reasonable time, DonkeyBuddy will take it as a
failure. So you should set the *AJAX timeout* option to a
suitable value on your condition.</li>
<li>If you are still in trouble, use the popup-windows mode instead.</li>
</ul></li>
<li><p><strong>Why a failure notification pops up when the downloading task has
acturally been added ?</strong></p>

<ul>
<li>Take a look at the upper question.</li>
</ul></li>
<li><p><strong>How can I send the selected links to MLDonkey in one click ?</strong></p>

<ul>
<li>Click on this extension's icon in the address bar.</li>
</ul></li>
</ol>

<h2>Change log</h2>

<blockquote>
  <p><strong>version 1.1.0 (2011-05-15 Sunday)</strong>
  Enable batch downloading for VeryCD.com.</p>
  
  <p><strong>version 1.0.1 (2010-09-25 Saturday)</strong>
  1.  Fix the problem that the image in the option page doesn't display
      as is expected.
  2.  Set the default value of the option *AJAX timeout* to 1000.</p>
  
  <p><strong>version 1.0 (2010-09-24 Friday)</strong> Initial release.</p>
</blockquote>

