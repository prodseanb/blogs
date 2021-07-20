<?php 
$basedir = '../../';
include($basedir.'includes/header.php'); 
?> 

      <section>

      <div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
</span></code></pre></div></div>

<div style="width:100%;height:0;padding-bottom:100%;position:relative;"><iframe src="https://giphy.com/embed/YQduDHR3pMlwunQptu" width="100%" height="100%" style="position:absolute" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div><p><a href="https://giphy.com/gifs/PhaseZwo-YQduDHR3pMlwunQptu"></a></p>
<h1>CTFLearn POST Practice</h1>
<p>POST Practice is a <a href="https://ctflearn.com/">CTFLearn</a> web security challenge. 
In this short walkthrough, we will learn how to send a basic POST request using Python.
</p>

<h3>Challenge:</h3>
<p>This challenge specifically requires us to authenticate the website via POST.
<br/><br/>
Link to challenge: <a href="http://165.227.106.113/post.php">http://165.227.106.113/post.php</a>.
</p>
<h3>Solution:</h3>
<p>When we visit the link, the page shows:</p>
<pre><code>This site takes POST data that you have not submitted!</pre></code>
<p>Inspecting the source code, we get random credentials.</p>
<pre><code>&#8249;!-- username: admin | password: 71urlkufpsdnlkadsf --&#8250;</pre></code>

<p>We can use these credentials to send a POST request. We will be taking the Pythonic approach.
<br/><br/>
First, we need to install the <a href="https://pypi.org/project/requests/">requests</a> library.      
</p>
<pre><code>pip3 install requests</pre></code>

<p>Create a <code>.py</code> file. I'm going to call this file <b>login.py</b>.
<br/><br/>
Within this file, we first need to import <b>requests</b>.
</p>
<pre><code><b>File: login.py</b>
import requests
</pre></code>
<p>Then, we need to set the URL of the challenge site, which will be used by our requests library
to send a POST request.</p>

<pre><code>url = 'http://165.227.106.113/post.php'</pre></code>

<p>
We also need to specify the credentials that we found in the site's source code by putting them in a 
dictionary.
</p>

<pre><code>
data = {
    'username': 'admin',
    'password': '71urlkufpsdnlkadsf'
}
</pre></code>

<p>
Then, we're going to utilize <b>requests</b> to send a POST request to the URL we supplied, and
authenticate using the <b>data</b> dictionary, which contains our credentials.
</p>
<pre><code>
response = requests.post(
    url, data=data
)
</pre></code>
<p>
Finally, we're going to take the content of the initialized <b>response</b> and display the result.
</p>

<pre><code>print(response.content)</pre></code>

<h3>The full code:</h3>

<pre><code>
import requests

url = 'http://165.227.106.113/post.php'

data = {
    'username': 'admin',
    'password': '71urlkufpsdnlkadsf'
}
response = requests.post(
    url, data=data
)
print(response.content)
</pre></code>

<p>This gives us the flag:</p>
<pre><code>b'&#8249;h1&#8250;flag{p0st_d4t4_4ll_d4y}&#8249;/h1&#8250;'</pre></code>
<p>That's it! We have successfully owned this challenge.
<br/><br/>
For more CTFLearn writeups, check out <a href="https://github.com/prodseanb/ctflearn-writeups">this repostitory</a>.
</p>
<p><img src="https://user-images.githubusercontent.com/59718043/126025334-bd226787-4374-4c87-bf63-a945131a9b8c.jpg" alt="game over" /></p>

<h1>Sources</h1>

<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Subject</th>
<th>Link</th>
</tr>
</thead>
<tbody>
<tr>
<td>CTFLearn</td>
<td><a href="https://ctflearn.com/">Site</a></td>
</tr>
<tr>
<td>Requests Library</td>
<td><a href="https://pypi.org/project/requests/">pypi</a></td>
</tr>
</tbody>
</table>

<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Media</th>
</tr>
</thead>
<tbody>
<tr>
<td><a href="https://giphy.com/gifs/PhaseZwo-YQduDHR3pMlwunQptu">Hacker GIF by PhaseZwo via GIPHY</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/game-over">Game Over Vectors by Vecteezy</a></td>
</tr>
</tbody>
</table>

<div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
## @Date: Jul 20 2021
</span></code></pre></div></div>

<div class="page-nav">
<a href="/index.php" class="previous round">&#8249;Home</a>
<!--<a href="#" class="next round">Next&#8250;</a>-->
</div>

      </section>
<?php 
$basedir = '../../';
include($basedir.'includes/footer.php'); 
?> 
