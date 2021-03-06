# UltraBlog.vim v2.3.0 Released With Templates

<p>I released the new version 2.3.0 of <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a> last week. The main improvement in this version is that templates are introduced in.</p>

<p>Templates are simply HTML strings, they are used to preview posts/pages in the browser locally. This feature is a reparation for the remote previewing, due to the limit of the API, users cannot send a post to Wordpress as draft and preview it without affecting the post status if the post has been published. With templates, they do not have to send drafts to blogs to preview the final effect, but preview drafts directly in the browser in a pre-defined style. Templates can be created as many as users like and their looks can be customized with CSS, HTML and Javascript.</p>

<p>The following illustrations show the content and final effect of the default template:</p>

<p>
<div style="display:table;">
    <span style="display:table-cell;"><a href="https://picasaweb.google.com/lh/photo/bixn15q6ujUIQmamJDm7lQ?feat=embedwebsite"><img src="https://lh4.googleusercontent.com/-RCCNuSJbvsc/TfX_QuWobsI/AAAAAAAABuY/2eeov12lzX0/s288/ultrablog_default_template.png" title="The Content of The Default Template" height="180" width="288" /></a></span>
    <span style="display:table-cell;width:10px;"></span>
    <span style="display:table-cell;"><a href="https://picasaweb.google.com/lh/photo/B0fBmAnyW2IAYDFcWb2e7Q?feat=embedwebsite"><img src="https://lh6.googleusercontent.com/-d0B7UEfZEYs/TfYEEJobpfI/AAAAAAAABug/OVYAB-imXlE/s288/ultrablog_template_effect.png" title="The Final Effect of The Default Template" height="180" width="288" /></a></span>
</div>
</p>

<p>Templates should be formatted as valid Python template strings, that is, use the following avaliable placeholders and escape any literal '%' with another '%':</p>

<blockquote>
  <p>%(title)s <br />
     The title of the current post/page.</p>
  
  <p>%(content)s <br />
     The content of the current post/page.</p>
  
  <p>%% <br />
     A literal '%'.</p>
</blockquote>

<p>Users can take the default template whose name is 'default' as an example for writing their own templates. The default template can be changed to another one by setting the name of that template to the option <strong>ub_default_template</strong>.</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

