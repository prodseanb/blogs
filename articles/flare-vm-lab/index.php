<?php 
$basedir = '../../';
include($basedir.'includes/header.php'); 
?> 

      <section>

      <div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
</span></code></pre></div></div>

<div style="width:100%;height:0;padding-bottom:75%;position:relative;"><iframe src="https://giphy.com/embed/FmLecXUlTqZ6o" width="100%" height="100%" style="position:absolute" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div><p><a href="https://giphy.com/gifs/transparent-windows-virus-FmLecXUlTqZ6o"></a></p>
<h1 id="ctflearn">How To Set Up a Malware Analysis Lab</h1>
<p>
Have you ever considered messing around with a malware sample from sites like <a href="https://www.vx-underground.org/">vx-underground</a>? Do you want to set up an isolated environment where you could deploy your own malicious program
to analyze its impact on the host/network? Don't worry, the process is fairly simple, and I will guide you through the whole thing.

</p>

<h3>Prerequisites</h3>

<li>VMware Workstation</li>
<li>REMnux OVA (you can download the OVA from <a href="https://remnux.org/">here</a>)</li>
<li>Windows 10 ISO</li>

<br/><br/>
<p>
Let's get started!
</p>

<br/><br/>
<p><img src="https://user-images.githubusercontent.com/59718043/167312748-426ee59c-7583-4866-a6f2-6c9d8c4aa9dd.png" /></p>
<p>
First, make sure that both VMs are installed and configured with basic settings on your hypervisor. Don't worry about setting up an isolated network right now because we will need to install a
couple of things before shutting off the network connection.
</p>

<p>
After installing both VMs, head over to your REMnux VM and update its packages:
</p>
<pre><code>$ remnux update
$ remnux upgrade
</pre></code>
<p>At this point, we can now enable <b>Host-Only</b> on the REMnux VM.</p><br/>
<h3>INetSim</h3>
<p>Because we're eventually going to shut off the network to isolate both VMs, we will need to simulate common Internet services to be able to analyze binaries that require network connection.
This can be achieved by using <a href="https://www.inetsim.org/">INetSim</a>.

<br/><br/>
To enable INetSim's services and bind them to an address, we will configure <b>/etc/inetsim/inetsim.conf</b>.

<ol type="1"><li>Run <code>sudo nano /etc/inetsim/inetsim.conf</code> to enable all the required services by uncommenting it out. Delete the <code>#</code> in front of the service to enable it.
The <b>start_service</b> section should look like the example below.</li><br/>
<pre><code>#########################################
# start_service
#
# The services to start
#
# Syntax: start_service &LT;service name&GT;
#
# Default: none
#
# Available service names are:
# dns, http, smtp, pop3, tftp, ftp, ntp, time_tcp,
# time_udp, daytime_tcp, daytime_udp, echo_tcp,
# echo_udp, discard_tcp, discard_udp, quotd_tcp,
# quotd_udp, chargen_tcp, chargen_udp, finger,
# ident, syslog, dummy_tcp, dummy_udp, smtps, pop3s,
# ftps, irc, https
#
start_service dns
start_service http
start_service https
start_service smtp
start_service smtps
start_service pop3
start_service pop3s
start_service ftp
start_service ftps
start_service tftp
start_service irc
start_service ntp
start_service finger
start_service ident
start_service syslog
start_service time_tcp
start_service time_udp
start_service daytime_tcp
start_service daytime_udp
start_service echo_tcp
start_service echo_udp
start_service discard_tcp
start_service discard_udp
start_service quotd_tcp
start_service quotd_udp
start_service chargen_tcp
start_service chargen_udp
start_service dummy_tcp
start_service dummy_udp


#########################################
</pre></code>
<br/>
<li>The next section we'll need to modify is the <b>service_bind_address</b>. This contains the IP address that we want to bind INetSim to. Uncomment it out and replace the address with the VM's address.</li>
<br/>
<pre><code>#########################################
# service_bind_address
#
# IP address to bind services to
#
# Syntax: service_bind_address &LT;IP address&GT;
#
# Default: 127.0.0.1
#
service_bind_address	192.168.42.129


#########################################
</pre></code>
<br/>
<li><b>dns_default_ip</b> is the next section we need to configure to bind DNS to an address. Same as the last section, we will uncomment it out and replace the address with the VM's address.</li>
<br/>
<pre><code>#########################################
# dns_default_ip
#
# Default IP address to return with DNS replies
#
# Syntax: dns_default_ip &LT;IP address&GT;
#
# Default: 127.0.0.1
#
dns_default_ip		192.168.42.129


#########################################
</pre></code>
<br/>
<li>Save the file and exit. Next, we will disable the <b>system-resolve</b> service so that it doesn't interfere with INetSim. We will also mask the service so that it doesn't automatically start on reboot.
Finally, we will stop the service from running.</li>
<br/>
<pre><code>$ sudo systemctl disable systemd-resolved
$ sudo systemctl mask systemd-resolved
$ sudo systemctl stop systemd-resolved
</pre></code>
<br/>
<li>Run INetSim.</li>
<br/>
<pre><code>$ sudo inetsim
INetSim 1.3.2 (2020-05-19) by Matthias Eckert & Thomas Hungenberg
Using log directory:      /var/log/inetsim/
Using data directory:     /var/lib/inetsim/
Using report directory:   /var/log/inetsim/report/
Using configuration file: /etc/inetsim/inetsim.conf
Parsing configuration file.
Configuration file parsed successfully.
=== INetSim main process started (PID 2085) ===
Session ID:     2085
Listening on:   192.168.42.129
Real Date/Time: 2022-05-08 15:14:00
Fake Date/Time: 2022-05-08 15:14:00 (Delta: 0 seconds)
 Forking services...
  * dns_53_tcp_udp - started (PID 2089)
  * finger_79_tcp - started (PID 2101)
  * syslog_514_udp - started (PID 2103)
  * ntp_123_udp - started (PID 2100)
  * time_37_udp - started (PID 2105)
  * irc_6667_tcp - started (PID 2099)
  * ident_113_tcp - started (PID 2102)
  * time_37_tcp - started (PID 2104)
  * daytime_13_tcp - started (PID 2106)
  * daytime_13_udp - started (PID 2107)
  * discard_9_tcp - started (PID 2110)
  * tftp_69_udp - started (PID 2098)
  * echo_7_udp - started (PID 2109)
  * echo_7_tcp - started (PID 2108)
  * chargen_19_udp - started (PID 2115)
  * chargen_19_tcp - started (PID 2114)
  * quotd_17_tcp - started (PID 2112)
  * ftps_990_tcp - started (PID 2097)
  * discard_9_udp - started (PID 2111)
  * quotd_17_udp - started (PID 2113)
  * dummy_1_udp - started (PID 2117)
  * smtps_465_tcp - started (PID 2093)
  * smtp_25_tcp - started (PID 2092)
  * pop3s_995_tcp - started (PID 2095)
  * https_443_tcp - started (PID 2091)
  * ftp_21_tcp - started (PID 2096)
  * dummy_1_tcp - started (PID 2116)
  * pop3_110_tcp - started (PID 2094)
  * http_80_tcp - started (PID 2090)
 done.
Simulation running.
</pre></code>
<br/>
<li>Set the Windows VM to <b>Host-Only</b> for now. We are going to test INetSim's services.</li>
<br/>
<li>Set a <b>static IP</b> on the Windows VM and set both the <b>DNS</b> and <b>default gateway</b> to the address where INetSim is listening on.</li>
<br/>
<li>Upon opening a browser and entering a URL on the Windows VM, INetSim should give us a fake page that says something like: <i>“This is the default HTML page for INetSim HTTP server fake mode.”</i>
This verifies that INetSim is working. Time to take a coffee break :)</li>
</ol>
Before proceeding to the next section, take a snapshot of both VMs. A backup version will always come in handy if things go wrong.
</p>
<br/><br/>
<h3>Flare VM</h3>
<p><img src="https://raw.githubusercontent.com/mandiant/flare-vm/master/flarevm.png" /></p>
<p><a href="https://github.com/mandiant/flare-vm">Flare VM</a> is a Windows-based collection of open-source projects for malware analysis, incident response, penetration testing, etc. 
We will be installing this framework on our Windows VM.
<ol type="1"><li>First things first, add another network adapter to your Windows VM and set it to <b>NAT</b>. At this point, once you receive a connection, disable the Host-Only adapter within the VM so that 
INetSim doesn't interfere with our installation. In my case, I had to disable <b>Ethernet0</b>.</li>
<br/>
<li><a href="https://support.microsoft.com/en-us/windows/turn-off-defender-antivirus-protection-in-windows-security-99e6004f-c54c-8509-773c-a4d776b77960">Turn off Windows antivirus protection in Windows Security.</a></li>
<br/>
<li>Download/copy <a href="https://raw.githubusercontent.com/mandiant/flare-vm/master/install.ps1">this script</a> onto your Windows VM and name it <code>install.ps1</code>.</li>
<br/>
<li>Open <b>PowerShell</b> as an Administrator on your Windows VM.</li>
<br/>
<li>Change into the directory where the installer script is located and unblock the script by running:</li>
<br/>
<pre><code>Unblock-File .\install.ps1
</pre></code>
<li>Enable script execution by running:</li>
<br/>
<pre><code>Set-ExecutionPolicy Unrestricted
</pre></code>
<li>Finally, execute the script:</li>
<br/>
<pre><code>.\install.ps1
</pre></code>
<br/>
<li>The installation may take a while. According to the developer, it takes about 30-40 minutes, so have yourself another coffee break :)</li>
<br/>
<li>Once the installation is finished, you should be able to see your desktop background changed to Flare VM's logo. Remove the <b>NAT</b> adapter from the Windows VM and make sure that the <b>Host-Only</b> adapter is 
still set. Enable the appropriate network adapter within the VM to initiate the isolated environment. In my case, I re-enabled <b>Ethernet0</b>.</li>
</ol>
</p>
<p>
Before doing anything else, please do not forget to update both VM's networking settings to <b>Host-Only</b> and take a snapshot. Both of these steps are crucial in making sure that the virtual network is completely isolated
and prepared for analysis.
</p>
<p>
That's it! You have successfully built a working malware analysis lab. Within the <b>Flare shortcut</b> on your Desktop you will see a folder named <i>PMALabs</i>. This contains all the labs and binaries from 
the hands-on guide book <a href="https://www.amazon.ca/Practical-Malware-Analysis-Hands-Dissecting/dp/1593272901">Practical Malware Analysis</a>. I would suggest following along with the book as you explore the 
exciting domain of malware analysis. Good luck on your journey!
</p>

<br/><br/>
<h1>Disclaimer</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/129311817-b1f90587-c5f0-467a-975e-a640b7570bbe.jpg" /></p>
<p>This tutorial has provided material that can be utilized in nefarious ways. Under no circumstances shall the author be liable to any damages or illegal acts through the use of any content from this site.</p>
<br/>
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
<td>Building a Custom Malware Analysis Lab Environment</td>
<td><a href="https://www.sentinelone.com/labs/building-a-custom-malware-analysis-lab-environment/">SentinelLABS</a></td>
</tr>
<tr>
<td>Flare VM</td>
<td><a href="https://github.com/mandiant/flare-vm">Mandiant</a></td>
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
<td><a href="https://giphy.com/gifs/transparent-windows-virus-FmLecXUlTqZ6o">Windows Virus GIF by Neural Entropy via GIPHY</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/warning">Warning Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://raw.githubusercontent.com/mandiant/flare-vm/master/flarevm.png">Flare VM logo</a></td>
</tr>
</tbody>
</table>

<div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
## @Date: Aug 13 2021
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
